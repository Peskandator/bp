<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921111820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP depreciation_increased_year_tax, DROP depreciation_increased_year_accounting');
        $this->addSql('ALTER TABLE depreciation_accounting ADD disposal_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE depreciation_tax ADD disposal_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD depreciation_increased_year_tax INT DEFAULT NULL, ADD depreciation_increased_year_accounting INT DEFAULT NULL');
        $this->addSql('ALTER TABLE depreciation_accounting DROP disposal_date');
        $this->addSql('ALTER TABLE depreciation_tax DROP disposal_date');
    }
}
