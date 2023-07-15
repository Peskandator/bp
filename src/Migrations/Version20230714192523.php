<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230714192523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE acquisition CHANGE code code INT DEFAULT NULL');
        $this->addSql('ALTER TABLE place CHANGE location_id location_id INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE place CHANGE location_id location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acquisition CHANGE code code INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
