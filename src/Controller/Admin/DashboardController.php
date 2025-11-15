<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Entity\Blog;
use App\Entity\Client;
use App\Entity\Plan;
use App\Entity\Quote;
use App\Entity\Subscription;
use App\Entity\Invoice;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sassify');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', Users::class);

        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Articles de blog', 'fa fa-blog', Blog::class);

        yield MenuItem::section('Abonnements & Facturation');
        yield MenuItem::linkToCrud('Plans', 'fa fa-credit-card', Plan::class);
        yield MenuItem::linkToCrud('Abonnements', 'fa fa-cart-plus', Subscription::class);
        yield MenuItem::linkToCrud('Factures', 'fa fa-file-invoice', Invoice::class);

        yield MenuItem::section('Navigation');
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-arrow-left', '/');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
