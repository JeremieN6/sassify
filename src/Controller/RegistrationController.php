<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use App\Service\SendMailService;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // On génère le jwt de l'utilisateur
            // On crée le header
            $header = [
                'type' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // On envoie un mail (temporairement désactivé pour les tests)
            /*$mail->send(
                'no-reply-sassify@sassify.fr',
                $user->getEmail(),
                'Activation de votre compte Sassify',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );*/

            return $security->login($user, UsersAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verification/{token}', name:'verification_user')]
    public function verifyUser($token, JWTService $jwt, EntityManagerInterface $em): Response
    {
        // On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            // On récupère le Payload
            $payload = $jwt->getPayload($token);

            // On récupère le user du token
            $user = $em->getRepository(Users::class)->find($payload['user_id']);

            // On vérifie si le user n'a pas encore activé son compte
            if($user && !$user->isVerified())
            {
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Utilisateur activé 🚀 !');
                return $this->redirectToRoute('app_home');
            }
        }
        // Ici un problème se pose sur le token
        $this->addFlash('danger', 'Le token est invalid, ou à expiré !');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoieverif', name:'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail): Response
    {
        $user = $this->getUser();

        if(!$user || !$user instanceof Users){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page ⛔ !');
            return $this->redirectToRoute('app_login');
        }

        if($user->isVerified())
        {
            $this->addFlash('warning', 'Cet utilisateur est déja activé !');
            return $this->redirectToRoute('app_home');
        }

        // On génère le jwt de l'utilisateur
        // On crée le header
        $header = [
            'type' => 'JWT',
            'alg' => 'HS256'
        ];

        // On crée le payload
        $payload = [
            'user_id' => $user->getId()
        ];

        // On génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // On envoie un mail
        $mail->send(
            'no-reply-sassify@sassify.fr',
            $user->getEmail(),
            'Activation de votre compte Sassify',
            'register',
            [
                'user' => $user,
                'token' => $token
            ]
        );

        $this->addFlash('success', 'Email de vérification envoyé ✅ !');
        return $this->redirectToRoute('app_home');
    }
}
