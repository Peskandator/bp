<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224102211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movement (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, type INT NOT NULL, value DOUBLE PRECISION NOT NULL, date DATE NOT NULL, account_credited VARCHAR(255) DEFAULT NULL, account_debited VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_F4DD95F75DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F75DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE asset ADD has_tax_depreciations TINYINT(1) NOT NULL, ADD is_included TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F75DA1941');
        $this->addSql('DROP TABLE movement');
        $this->addSql('ALTER TABLE asset DROP has_tax_depreciations, DROP is_included');
    }
}
