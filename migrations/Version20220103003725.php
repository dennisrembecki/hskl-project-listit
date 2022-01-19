<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220103003725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liste (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, rating VARCHAR(255) NOT NULL, created DATETIME DEFAULT NULL, private BOOLEAN DEFAULT NULL, sort VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_FCF22AF461220EA6 ON liste (creator_id)');
        $this->addSql('CREATE TABLE liste_element (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, liste_id INTEGER NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, img VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_F07AC668E85441D8 ON liste_element (liste_id)');
        $this->addSql('CREATE INDEX IDX_F07AC66861220EA6 ON liste_element (creator_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, element_id INTEGER DEFAULT NULL, value INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5A10856461220EA6 ON vote (creator_id)');
        $this->addSql('CREATE INDEX IDX_5A1085641F1F2A24 ON vote (element_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE liste');
        $this->addSql('DROP TABLE liste_element');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vote');
    }
}
