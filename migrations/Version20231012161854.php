<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012161854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, categorie_id, title, description, cover FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, categorie_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_1D5EF26FBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO movie (id, categorie_id, title, description, cover) SELECT id, categorie_id, title, description, cover FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('CREATE INDEX IDX_1D5EF26FBCF5E72D ON movie (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie ADD COLUMN path VARCHAR(255) NOT NULL');
    }
}
