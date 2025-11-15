<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722192309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(10) NOT NULL, country VARCHAR(100) DEFAULT NULL, siret VARCHAR(20) DEFAULT NULL, tva_number VARCHAR(30) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C7440455A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, client_id INT NOT NULL, quote_number VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, total_ht NUMERIC(10, 2) NOT NULL, tva_rate NUMERIC(5, 2) NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', estimated_start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', estimated_duration INT DEFAULT NULL, estimation_data JSON DEFAULT NULL, payment_terms LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_6B71CBF4AC28B117 (quote_number), INDEX IDX_6B71CBF4A76ED395 (user_id), INDEX IDX_6B71CBF419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote_item (id INT AUTO_INCREMENT NOT NULL, quote_id INT NOT NULL, description VARCHAR(255) NOT NULL, quantity NUMERIC(8, 2) NOT NULL, unit VARCHAR(20) NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, sort_order INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_8DFC7A94DB805178 (quote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE quote_item ADD CONSTRAINT FK_8DFC7A94DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF419EB6921');
        $this->addSql('ALTER TABLE quote_item DROP FOREIGN KEY FK_8DFC7A94DB805178');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_item');
    }
}
