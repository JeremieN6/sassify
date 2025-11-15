<?php


namespace App\Controller;


use App\Entity\Invoice;
use App\Entity\Subscription;
use App\Repository\InvoiceRepository;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;


class WebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'app_webhook_stripe')]
    public function index(
        LoggerInterface $logger,
        ManagerRegistry $doctrine,
        UsersRepository $usersRepository,
        PlanRepository $planRepository,
        SubscriptionRepository $subscriptionRepository,
        InvoiceRepository $invoiceRepository,
        EntityManagerInterface $em): Response
    {
        Stripe::setApiKey($this->getParameter('stripe_sk'));
        $event = null;


        // Check request
        $endpoint_secret = $this->getParameter('stripe_webhook_secret');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
       
        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            $logger->info('Webhook Stripe Invalid payload');
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            $logger->info('Webhook Stripe Invalid signature');
            http_response_code(403);
            exit();
        }


        // Handle the event
        $logger->info('Webhook Stripe - Event type received: ' . $event->type);
        switch ($event->type) {
            case 'checkout.session.completed':
                $logger->info('Webhook Stripe connect checkout.session.completed');
                $session = $event->data->object;
                $subscriptionId = $session->subscription;


                $stripe = new StripeClient($this->getParameter('stripe_sk'));
                $subscriptionStripe = $stripe->subscriptions->retrieve($subscriptionId, array());
                $planId = $subscriptionStripe->plan->id;


                // Get user
                $customerEmail = $session->customer_details->email;
                $user = $usersRepository->findOneBy(['email' => $customerEmail]);
                if (!$user) {
                    $logger->info('Webhook Stripe user not found');
                    http_response_code(404);
                    exit();
                }


                // Disable old subscription
                $logger->info('Webhook Stripe - User ID: ' . $user->getId());
                $activeSub = $subscriptionRepository->findActiveSub($user->getId());
                if ($activeSub) {
                    $logger->info('Webhook Stripe - Cancelling old subscription: ' . $activeSub->getStripeId());
                    StripeSubscription::update(
                        $activeSub->getStripeId(), [
                            'cancel_at_period_end' => true,  // ✅ Annuler à la fin de la période
                        ]
                    );

                    $activeSub->setIsActive(false);
                    $em->persist($activeSub);
                }


                // Get plan
                $plan = $planRepository->findOneBy(['stripeId' => $planId]);
                if (!$plan) {
                    $logger->info('Webhook Stripe plan not found');
                    http_response_code(404);
                    exit();
                }


                // Create subscription
                $logger->info('Webhook Stripe - Creating new subscription for plan: ' . $plan->getName());
                $subscription = new Subscription();
                $subscription->setPlan($plan);
                $subscription->setStripeId($subscriptionStripe->id);
                $subscription->setCurrentPeriodStart(new \Datetime(date('c', $subscriptionStripe->current_period_start)));
                $subscription->setCurrentPeriodEnd(new \Datetime(date('c', $subscriptionStripe->current_period_end)));
                $subscription->setUser($user);
                $subscription->setIsActive(true);
                $user->setStripeId($session->customer);
                $logger->info('Webhook Stripe - Subscription isActive set to: ' . ($subscription->isIsActive() ? 'true' : 'false'));
                $em->persist($subscription);
                $em->flush();
                $logger->info('Webhook Stripe - Subscription created with ID: ' . $subscription->getId());

                // Créer l'invoice immédiatement si elle n'existe pas déjà
                $logger->info('Webhook Stripe - Checking if invoice needs to be created');
                try {
                    // Récupérer la dernière invoice de cette session
                    $invoiceStripe = $stripe->invoices->all([
                        'subscription' => $subscriptionId,
                        'limit' => 1
                    ])->data[0] ?? null;

                    if ($invoiceStripe) {
                        // Vérifier si l'invoice existe déjà en base
                        $existingInvoice = $em->getRepository(Invoice::class)->findOneBy(['stripeId' => $invoiceStripe->id]);

                        if ($existingInvoice) {
                            $logger->info('Webhook Stripe - Invoice already exists, updating with subscription');
                            // Mettre à jour l'invoice existante avec la subscription
                            $existingInvoice->setSubscription($subscription);
                            $em->persist($existingInvoice);
                            $em->flush();
                            $logger->info('Webhook Stripe - Invoice updated with subscription ID: ' . $existingInvoice->getId());
                        } else {
                            $logger->info('Webhook Stripe - Creating new invoice from checkout session');
                            $invoice = new Invoice();
                            $invoice->setStripeId($invoiceStripe->id);
                            $invoice->setSubscription($subscription);
                            $invoice->setNumber($invoiceStripe->number);
                            $invoice->setAmountPaid($invoiceStripe->amount_paid);
                            $invoice->setHostedInvoiceUrl($invoiceStripe->hosted_invoice_url);

                            $em->persist($invoice);
                            $em->flush();
                            $logger->info('Webhook Stripe - Invoice created from checkout with ID: ' . $invoice->getId());
                        }
                    }
                } catch (\Exception $e) {
                    $logger->error('Webhook Stripe - Error creating invoice: ' . $e->getMessage());
                }

                break;
            case 'invoice.paid':
                $logger->info('Webhook Stripe - invoice.paid event received');
                $logger->info('Webhook Stripe - Invoice data: ' . json_encode($event->data->object));
                $subscriptionId = $event->data->object->subscription;
                if (!$subscriptionId) {
                    $logger->info('Webhook Stripe - No subscription in invoice.paid');

                    // Vérifier si l'invoice existe déjà
                    $existingInvoice = $em->getRepository(Invoice::class)->findOneBy(['stripeId' => $event->data->object->id]);
                    if ($existingInvoice) {
                        $logger->info('Webhook Stripe - Invoice already exists, skipping creation');
                        break;
                    }

                    // Créer l'invoice sans subscription (pour les paiements one-time)
                    $logger->info('Webhook Stripe - Creating invoice without subscription');
                    $invoice = new Invoice();
                    $invoice->setStripeId($event->data->object->id);
                    $invoice->setNumber($event->data->object->number);
                    $invoice->setAmountPaid($event->data->object->amount_paid);
                    $invoice->setHostedInvoiceUrl($event->data->object->hosted_invoice_url);

                    $em->persist($invoice);
                    $em->flush();
                    $logger->info('Webhook Stripe - Invoice created without subscription, ID: ' . $invoice->getId());
                    break;
                }
                $logger->info('Webhook Stripe - Processing invoice for subscription: ' . $subscriptionId);


                $subscription = null;
                for ($i = 0; $i <= 4 && $subscription === null; $i++) {
                    $subscription = $subscriptionRepository->findOneBy(['stripeId' => $subscriptionId]);
                    if ($subscription) {
                        break;
                    }
                    sleep(5);
                }


                if ($subscription) {
                    // Vous avez trouvé la subscription, vous pouvez maintenant obtenir son ID
                    $subscriptionId = $subscription->getId();
                    $logger->info('Webhook Stripe - Subscription found, ID: ' . $subscriptionId);
                } else {
                    $logger->info('Webhook Stripe - Subscription not found in the database');
                    break;
                }

                // Vérifier si l'invoice existe déjà
                $existingInvoice = $em->getRepository(Invoice::class)->findOneBy(['stripeId' => $event->data->object->id]);
                if ($existingInvoice) {
                    $logger->info('Webhook Stripe - Invoice already exists, updating with subscription');
                    $existingInvoice->setSubscription($subscription);
                    $em->persist($existingInvoice);
                    $em->flush();
                    $logger->info('Webhook Stripe - Invoice updated with subscription');
                } else {
                    $logger->info('Webhook Stripe - Creating invoice for subscription: ' . $subscriptionId);
                    $invoice = new Invoice();
                    $invoice->setStripeId($event->data->object->id);
                    $invoice->setSubscription($subscription);
                    $invoice->setNumber($event->data->object->number);
                    $invoice->setAmountPaid($event->data->object->amount_paid);
                    $invoice->setHostedInvoiceUrl($event->data->object->hosted_invoice_url);

                    $em->persist($invoice);
                    $em->flush();
                    $logger->info('Webhook Stripe - Invoice created with ID: ' . $invoice->getId());
                }


                break;
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }


        http_response_code(200);


        $response = new Response('success');
        $response->headers->set('Content-Type', 'application/json');


        return $response;
    }
} 
