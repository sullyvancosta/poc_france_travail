<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250907170823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city ADD COLUMN use_for_france_travail BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__city AS SELECT id, name, postal_code, insee_code, department, france_travail_use_department FROM city');
        $this->addSql('DROP TABLE city');
        $this->addSql('CREATE TABLE city (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, insee_code VARCHAR(10) NOT NULL, department VARCHAR(10) NOT NULL, france_travail_use_department BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO city (id, name, postal_code, insee_code, department, france_travail_use_department) SELECT id, name, postal_code, insee_code, department, france_travail_use_department FROM __temp__city');
        $this->addSql('DROP TABLE __temp__city');
    }
}
