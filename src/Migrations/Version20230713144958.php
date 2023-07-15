<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713144958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_5E9E89CB77153098 on location');
        $this->addSql('DROP INDEX UNIQ_741D53CD77153098 on place');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E9E89CB77153098 ON location (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_741D53CD77153098 ON place (code)');
    }
}
