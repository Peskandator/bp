<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716171942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset_type (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, code INT NOT NULL, name VARCHAR(255) NOT NULL, series INT NOT NULL, step INT NOT NULL, INDEX IDX_68BA92E181257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, depreciation_group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code INT NOT NULL, account_asset VARCHAR(255) DEFAULT NULL, account_depreciation VARCHAR(255) DEFAULT NULL, account_repairs VARCHAR(255) DEFAULT NULL, INDEX IDX_64C19C181257D5D (entity_id), INDEX IDX_64C19C1C75DF7FD (depreciation_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depreciation_group (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, group_number INT NOT NULL, depreciation_method INT NOT NULL, years_to_depreciate INT DEFAULT NULL, months_to_depreciate INT DEFAULT NULL, is_coefficient TINYINT(1) NOT NULL, rate_first_year DOUBLE PRECISION NOT NULL, rate DOUBLE PRECISION NOT NULL, rate_increased_price DOUBLE PRECISION NOT NULL, INDEX IDX_6973654081257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset_type ADD CONSTRAINT FK_68BA92E181257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C181257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1C75DF7FD FOREIGN KEY (depreciation_group_id) REFERENCES depreciation_group (id)');
        $this->addSql('ALTER TABLE depreciation_group ADD CONSTRAINT FK_6973654081257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset_type DROP FOREIGN KEY FK_68BA92E181257D5D');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C181257D5D');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1C75DF7FD');
        $this->addSql('ALTER TABLE depreciation_group DROP FOREIGN KEY FK_6973654081257D5D');
        $this->addSql('DROP TABLE asset_type');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE depreciation_group');
    }
}
