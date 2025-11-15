<?php

namespace App\Controller\Admin;

use App\Entity\Quote;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quote::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', 'Utilisateur'),
            AssociationField::new('client', 'Client'),
            TextField::new('quoteNumber', 'N° Devis'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => Quote::STATUS_DRAFT,
                    'Envoyé' => Quote::STATUS_SENT,
                    'Accepté' => Quote::STATUS_ACCEPTED,
                    'Refusé' => Quote::STATUS_REFUSED,
                    'Expiré' => Quote::STATUS_EXPIRED,
                ]),
            TextField::new('title', 'Titre'),
            TextareaField::new('description', 'Description'),
            MoneyField::new('totalHt', 'Total HT')->setCurrency('EUR'),
            NumberField::new('tvaRate', 'Taux TVA (%)'),
            MoneyField::new('totalTtc', 'Total TTC')->setCurrency('EUR'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('expiresAt', 'Expire le'),
            DateTimeField::new('sentAt', 'Envoyé le')->hideOnForm(),
            DateTimeField::new('acceptedAt', 'Accepté le')->hideOnForm(),
            DateTimeField::new('estimatedStartDate', 'Début estimé'),
            NumberField::new('estimatedDuration', 'Durée (jours)'),
            TextareaField::new('paymentTerms', 'Conditions de paiement'),
            TextareaField::new('notes', 'Notes'),
        ];
    }
}
