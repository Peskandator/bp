<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225220108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement ADD depreciation_tax_id INT DEFAULT NULL, ADD depreciation_accounting_id INT DEFAULT NULL, ADD residual_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F72F7EE056 FOREIGN KEY (depreciation_tax_id) REFERENCES depreciation_tax (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F720B2AEE5 FOREIGN KEY (depreciation_accounting_id) REFERENCES depreciation_accounting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4DD95F72F7EE056 ON movement (depreciation_tax_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4DD95F720B2AEE5 ON movement (depreciation_accounting_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F72F7EE056');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F720B2AEE5');
        $this->addSql('DROP INDEX UNIQ_F4DD95F72F7EE056 ON movement');
        $this->addSql('DROP INDEX UNIQ_F4DD95F720B2AEE5 ON movement');
        $this->addSql('ALTER TABLE movement DROP depreciation_tax_id, DROP depreciation_accounting_id, DROP residual_price');
    }
}
