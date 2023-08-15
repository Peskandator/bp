<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813204719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disposal (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, code INT DEFAULT NULL, is_default TINYINT(1) NOT NULL, INDEX IDX_56D9A17781257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE disposal ADD CONSTRAINT FK_56D9A17781257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE acquisition DROP is_disposal');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C7FEC5C6B');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C7FEC5C6B FOREIGN KEY (disposal_id) REFERENCES disposal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C7FEC5C6B');
        $this->addSql('ALTER TABLE disposal DROP FOREIGN KEY FK_56D9A17781257D5D');
        $this->addSql('DROP TABLE disposal');
        $this->addSql('ALTER TABLE acquisition ADD is_disposal TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C7FEC5C6B FOREIGN KEY (disposal_id) REFERENCES acquisition (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
