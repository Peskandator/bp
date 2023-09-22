<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918121923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE depreciation_accounting (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, depreciation_group_id INT NOT NULL, year INT DEFAULT NULL, depreciation_year INT DEFAULT NULL, depreciation_method INT NOT NULL, executable TINYINT(1) NOT NULL, executed TINYINT(1) NOT NULL, percentage DOUBLE PRECISION NOT NULL, is_coefficient TINYINT(1) NOT NULL, rate DOUBLE PRECISION NOT NULL, entry_price DOUBLE PRECISION NOT NULL, increased_entry_price DOUBLE PRECISION DEFAULT NULL, depreciation_amount DOUBLE PRECISION NOT NULL, depreciated_amount DOUBLE PRECISION NOT NULL, residual_price DOUBLE PRECISION NOT NULL, INDEX IDX_AF3973115DA1941 (asset_id), INDEX IDX_AF397311C75DF7FD (depreciation_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depreciation_tax (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, depreciation_group_id INT NOT NULL, year INT DEFAULT NULL, depreciation_year INT DEFAULT NULL, depreciation_method INT NOT NULL, executable TINYINT(1) NOT NULL, executed TINYINT(1) NOT NULL, percentage DOUBLE PRECISION NOT NULL, is_coefficient TINYINT(1) NOT NULL, rate DOUBLE PRECISION NOT NULL, entry_price DOUBLE PRECISION NOT NULL, increased_entry_price DOUBLE PRECISION DEFAULT NULL, depreciation_amount DOUBLE PRECISION NOT NULL, depreciated_amount DOUBLE PRECISION NOT NULL, residual_price DOUBLE PRECISION NOT NULL, INDEX IDX_54A2C4A45DA1941 (asset_id), INDEX IDX_54A2C4A4C75DF7FD (depreciation_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE depreciation_accounting ADD CONSTRAINT FK_AF3973115DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE depreciation_accounting ADD CONSTRAINT FK_AF397311C75DF7FD FOREIGN KEY (depreciation_group_id) REFERENCES depreciation_group (id)');
        $this->addSql('ALTER TABLE depreciation_tax ADD CONSTRAINT FK_54A2C4A45DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE depreciation_tax ADD CONSTRAINT FK_54A2C4A4C75DF7FD FOREIGN KEY (depreciation_group_id) REFERENCES depreciation_group (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depreciation_accounting DROP FOREIGN KEY FK_AF3973115DA1941');
        $this->addSql('ALTER TABLE depreciation_accounting DROP FOREIGN KEY FK_AF397311C75DF7FD');
        $this->addSql('ALTER TABLE depreciation_tax DROP FOREIGN KEY FK_54A2C4A45DA1941');
        $this->addSql('ALTER TABLE depreciation_tax DROP FOREIGN KEY FK_54A2C4A4C75DF7FD');
        $this->addSql('DROP TABLE depreciation_accounting');
        $this->addSql('DROP TABLE depreciation_tax');
    }
}
