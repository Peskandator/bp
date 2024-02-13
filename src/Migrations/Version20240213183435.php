<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213183435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C64D218E ON asset (location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
