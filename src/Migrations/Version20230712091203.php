<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712091203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acquisition (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, code INT NOT NULL, UNIQUE INDEX UNIQ_2FEB903377153098 (code), INDEX IDX_2FEB903381257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, code INT NOT NULL, UNIQUE INDEX UNIQ_5E9E89CB77153098 (code), INDEX IDX_5E9E89CB81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, code INT NOT NULL, UNIQUE INDEX UNIQ_741D53CD77153098 (code), INDEX IDX_741D53CD64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acquisition ADD CONSTRAINT FK_2FEB903381257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB81257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acquisition DROP FOREIGN KEY FK_2FEB903381257D5D');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB81257D5D');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD64D218E');
        $this->addSql('DROP TABLE acquisition');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE place');
    }
}
