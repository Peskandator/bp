<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230702181943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounting_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entity_user (id INT AUTO_INCREMENT NOT NULL, accounting_entity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C55F6F62D22E6A40 (accounting_entity_id), INDEX IDX_C55F6F62A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entity_user ADD CONSTRAINT FK_C55F6F62D22E6A40 FOREIGN KEY (accounting_entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE entity_user ADD CONSTRAINT FK_C55F6F62A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entity_user DROP FOREIGN KEY FK_C55F6F62D22E6A40');
        $this->addSql('ALTER TABLE entity_user DROP FOREIGN KEY FK_C55F6F62A76ED395');
        $this->addSql('DROP TABLE accounting_entity');
        $this->addSql('DROP TABLE entity_user');
    }
}
