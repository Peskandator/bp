<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507113948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting DROP accounted');
        $this->addSql('ALTER TABLE depreciation_tax DROP accounted');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting ADD accounted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE depreciation_tax ADD accounted TINYINT(1) NOT NULL');
    }
}
