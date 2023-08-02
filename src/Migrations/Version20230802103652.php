<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802103652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD depreciation_group_tax INT DEFAULT NULL, ADD depreciation_group_accounting INT DEFAULT NULL, ADD depreciated_amount_tax DOUBLE PRECISION DEFAULT NULL, ADD depreciated_amount_accounting DOUBLE PRECISION DEFAULT NULL, ADD units INT DEFAULT NULL, ADD depreciation_year_tax INT DEFAULT NULL, ADD depreciation_increased_year_tax INT DEFAULT NULL, DROP disposal_price_tax, DROP disposal_price_accounting, DROP inclusion_date');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C269E01B3 FOREIGN KEY (depreciation_group_tax) REFERENCES depreciation_group (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CE98263C6 FOREIGN KEY (depreciation_group_accounting) REFERENCES depreciation_group (id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C269E01B3 ON asset (depreciation_group_tax)');
        $this->addSql('CREATE INDEX IDX_2AF5A5CE98263C6 ON asset (depreciation_group_accounting)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C269E01B3');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CE98263C6');
        $this->addSql('DROP INDEX IDX_2AF5A5C269E01B3 ON asset');
        $this->addSql('DROP INDEX IDX_2AF5A5CE98263C6 ON asset');
        $this->addSql('ALTER TABLE asset ADD disposal_price_tax DOUBLE PRECISION DEFAULT NULL, ADD disposal_price_accounting DOUBLE PRECISION DEFAULT NULL, ADD inclusion_date DATE DEFAULT NULL, DROP depreciation_group_tax, DROP depreciation_group_accounting, DROP depreciated_amount_tax, DROP depreciated_amount_accounting, DROP units, DROP depreciation_year_tax, DROP depreciation_increased_year_tax');
    }
}
