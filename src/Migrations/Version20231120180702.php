<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120180702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting CHANGE rate rate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE depreciation_group CHANGE group_number group_number INT DEFAULT NULL, CHANGE rate_first_year rate_first_year DOUBLE PRECISION DEFAULT NULL, CHANGE rate rate DOUBLE PRECISION DEFAULT NULL, CHANGE rate_increased_price rate_increased_price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting CHANGE rate rate DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE depreciation_group CHANGE group_number group_number INT NOT NULL, CHANGE rate_first_year rate_first_year DOUBLE PRECISION NOT NULL, CHANGE rate rate DOUBLE PRECISION NOT NULL, CHANGE rate_increased_price rate_increased_price DOUBLE PRECISION NOT NULL');
    }
}
