<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230814101837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C6F52F3C FOREIGN KEY (acquisition_id) REFERENCES acquisition (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5CDA6A219 ON asset (place_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C6F52F3C');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CDA6A219');
        $this->addSql('DROP INDEX IDX_2AF5A5CDA6A219 ON asset');
        $this->addSql('ALTER TABLE asset DROP place_id');
    }
}
