<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508114017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE depreciations_accounting_data (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, year INT NOT NULL, data JSON NOT NULL, updated_at DATE NOT NULL, INDEX IDX_7A83532581257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE depreciations_accounting_data ADD CONSTRAINT FK_7A83532581257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciations_accounting_data DROP FOREIGN KEY FK_7A83532581257D5D');
        $this->addSql('DROP TABLE depreciations_accounting_data');
    }
}
