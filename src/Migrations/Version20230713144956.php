<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713144956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acquisition ADD is_default TINYINT(1) NOT NULL');
        $this->addSql('INSERT INTO acquisition (entity_id, name, code, is_default) 
            VAlUES(NULL, "Koupě", 1, 1),
            (NULL, "Vlastní výroba", 2, 1), 
            (NULL, "Vklad do podniku", 3, 1), 
            (NULL, "Dar", 4, 1), 
            (NULL, "Dotace", 5, 1),
            (NULL, "Jiné", 6, 1)
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM acquisition
            WHERE code IN (1,2,3,4,5,6)
        ');
        $this->addSql('ALTER TABLE acquisition DROP is_default');
    }
}
