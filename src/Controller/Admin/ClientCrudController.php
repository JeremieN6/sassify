<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', 'Utilisateur'),
            TextField::new('name', 'Nom'),
            TextField::new('company', 'Entreprise'),
            EmailField::new('email', 'Email'),
            TelephoneField::new('phone', 'Téléphone'),
            TextField::new('address', 'Adresse'),
            TextField::new('city', 'Ville'),
            TextField::new('postalCode', 'Code postal'),
            TextField::new('country', 'Pays'),
            TextField::new('siret', 'SIRET'),
            TextField::new('tvaNumber', 'N° TVA'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }
}
