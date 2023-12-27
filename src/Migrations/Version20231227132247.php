<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227132247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD entry_price DOUBLE PRECISION DEFAULT NULL, ADD increased_entry_price DOUBLE PRECISION DEFAULT NULL, DROP entry_price_tax, DROP increased_entry_price_tax, DROP entry_price_accounting, DROP increased_entry_price_accounting, DROP increase_date_accounting');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD entry_price_tax DOUBLE PRECISION DEFAULT NULL, ADD increased_entry_price_tax DOUBLE PRECISION DEFAULT NULL, ADD entry_price_accounting DOUBLE PRECISION DEFAULT NULL, ADD increased_entry_price_accounting DOUBLE PRECISION DEFAULT NULL, ADD increase_date_accounting DATE DEFAULT NULL, DROP entry_price, DROP increased_entry_price');
    }
}
