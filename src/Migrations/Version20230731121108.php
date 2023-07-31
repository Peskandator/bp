<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731121108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, asset_type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, disposal_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, inventory_number INT NOT NULL, inclusion_date DATE DEFAULT NULL, entry_date DATE DEFAULT NULL, entry_price_tax DOUBLE PRECISION DEFAULT NULL, increased_entry_price_tax DOUBLE PRECISION DEFAULT NULL, disposal_price_tax DOUBLE PRECISION DEFAULT NULL, entry_price_accounting DOUBLE PRECISION DEFAULT NULL, increased_entry_price_accounting DOUBLE PRECISION DEFAULT NULL, disposal_price_accounting DOUBLE PRECISION DEFAULT NULL, is_disposed TINYINT(1) NOT NULL, only_tax TINYINT(1) NOT NULL, producer VARCHAR(255) DEFAULT NULL, disposal_date DATE DEFAULT NULL, variable_symbol VARCHAR(255) DEFAULT NULL, invoice_number INT DEFAULT NULL, acquisition_id INT DEFAULT NULL, INDEX IDX_2AF5A5C81257D5D (entity_id), INDEX IDX_2AF5A5CA6A2CDC5 (asset_type_id), INDEX IDX_2AF5A5C12469DE2 (category_id), INDEX IDX_2AF5A5C6F52F3C (acquisition_id), INDEX IDX_2AF5A5C7FEC5C6B (disposal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C81257D5D FOREIGN KEY (entity_id) REFERENCES accounting_entity (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CA6A2CDC5 FOREIGN KEY (asset_type_id) REFERENCES asset_type (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C7FEC5C6B FOREIGN KEY (disposal_id) REFERENCES acquisition (id)');
        $this->addSql('ALTER TABLE acquisition ADD is_disposal TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE depreciation_group CHANGE prefix prefix VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C81257D5D');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CA6A2CDC5');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C12469DE2');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C7FEC5C6B');
        $this->addSql('DROP TABLE asset');
        $this->addSql('ALTER TABLE acquisition DROP is_disposal');
        $this->addSql('ALTER TABLE depreciation_group CHANGE prefix prefix VARCHAR(1) NOT NULL');
    }
}
