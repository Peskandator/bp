<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223095950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting ADD rate_format INT NOT NULL, DROP is_coefficient');
        $this->addSql('ALTER TABLE depreciation_group ADD rate_format INT NOT NULL, DROP is_coefficient');
        $this->addSql('ALTER TABLE depreciation_tax ADD rate_format INT NOT NULL, DROP is_coefficient');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting ADD is_coefficient TINYINT(1) NOT NULL, DROP rate_format');
        $this->addSql('ALTER TABLE depreciation_group ADD is_coefficient TINYINT(1) NOT NULL, DROP rate_format');
        $this->addSql('ALTER TABLE depreciation_tax ADD is_coefficient TINYINT(1) NOT NULL, DROP rate_format');
    }
}
