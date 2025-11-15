<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class SubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('stripeId', 'ID Stripe'),
            AssociationField::new('user', 'Utilisateur'),
            AssociationField::new('plan', 'Plan'),
            DateTimeField::new('currentPeriodStart', 'Début de période'),
            DateTimeField::new('currentPeriodEnd', 'Fin de période'),
            BooleanField::new('isActive', 'Actif'),
        ];
    }
}
