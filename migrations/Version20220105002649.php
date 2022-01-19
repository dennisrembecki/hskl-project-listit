<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220105002649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_liste (user_id INTEGER NOT NULL, liste_id INTEGER NOT NULL, PRIMARY KEY(user_id, liste_id))');
        $this->addSql('CREATE INDEX IDX_1E30D1ACA76ED395 ON user_liste (user_id)');
        $this->addSql('CREATE INDEX IDX_1E30D1ACE85441D8 ON user_liste (liste_id)');
        $this->addSql('DROP INDEX IDX_FCF22AF4CC3FBFA2');
        $this->addSql('DROP INDEX IDX_FCF22AF461220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__liste AS SELECT id, creator_id, name, category, type, rating, created, private, sort FROM liste');
        $this->addSql('DROP TABLE liste');
        $this->addSql('CREATE TABLE liste (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, category VARCHAR(255) NOT NULL COLLATE BINARY, type VARCHAR(255) NOT NULL COLLATE BINARY, rating VARCHAR(255) NOT NULL COLLATE BINARY, created DATETIME DEFAULT NULL, private BOOLEAN DEFAULT NULL, sort VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_FCF22AF461220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO liste (id, creator_id, name, category, type, rating, created, private, sort) SELECT id, creator_id, name, category, type, rating, created, private, sort FROM __temp__liste');
        $this->addSql('DROP TABLE __temp__liste');
        $this->addSql('CREATE INDEX IDX_FCF22AF461220EA6 ON liste (creator_id)');
        $this->addSql('DROP INDEX IDX_F07AC668E85441D8');
        $this->addSql('DROP INDEX IDX_F07AC66861220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__liste_element AS SELECT id, liste_id, creator_id, name, description, img, date, info FROM liste_element');
        $this->addSql('DROP TABLE liste_element');
        $this->addSql('CREATE TABLE liste_element (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, liste_id INTEGER NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL, img VARCHAR(255) DEFAULT NULL COLLATE BINARY, date DATETIME DEFAULT NULL, info VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_F07AC668E85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F07AC66861220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO liste_element (id, liste_id, creator_id, name, description, img, date, info) SELECT id, liste_id, creator_id, name, description, img, date, info FROM __temp__liste_element');
        $this->addSql('DROP TABLE __temp__liste_element');
        $this->addSql('CREATE INDEX IDX_F07AC668E85441D8 ON liste_element (liste_id)');
        $this->addSql('CREATE INDEX IDX_F07AC66861220EA6 ON liste_element (creator_id)');
        $this->addSql('DROP INDEX IDX_5A10856461220EA6');
        $this->addSql('DROP INDEX IDX_5A1085641F1F2A24');
        $this->addSql('CREATE TEMPORARY TABLE __temp__vote AS SELECT id, creator_id, element_id, value, type FROM vote');
        $this->addSql('DROP TABLE vote');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, element_id INTEGER DEFAULT NULL, value INTEGER NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_5A10856461220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A1085641F1F2A24 FOREIGN KEY (element_id) REFERENCES liste_element (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO vote (id, creator_id, element_id, value, type) SELECT id, creator_id, element_id, value, type FROM __temp__vote');
        $this->addSql('DROP TABLE __temp__vote');
        $this->addSql('CREATE INDEX IDX_5A10856461220EA6 ON vote (creator_id)');
        $this->addSql('CREATE INDEX IDX_5A1085641F1F2A24 ON vote (element_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_liste');
        $this->addSql('DROP INDEX IDX_FCF22AF461220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__liste AS SELECT id, creator_id, name, category, type, rating, created, private, sort FROM liste');
        $this->addSql('DROP TABLE liste');
        $this->addSql('CREATE TABLE liste (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, rating VARCHAR(255) NOT NULL, created DATETIME DEFAULT NULL, private BOOLEAN DEFAULT NULL, sort VARCHAR(255) DEFAULT NULL, favorit_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO liste (id, creator_id, name, category, type, rating, created, private, sort) SELECT id, creator_id, name, category, type, rating, created, private, sort FROM __temp__liste');
        $this->addSql('DROP TABLE __temp__liste');
        $this->addSql('CREATE INDEX IDX_FCF22AF461220EA6 ON liste (creator_id)');
        $this->addSql('CREATE INDEX IDX_FCF22AF4CC3FBFA2 ON liste (favorit_id)');
        $this->addSql('DROP INDEX IDX_F07AC668E85441D8');
        $this->addSql('DROP INDEX IDX_F07AC66861220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__liste_element AS SELECT id, liste_id, creator_id, name, description, img, date, info FROM liste_element');
        $this->addSql('DROP TABLE liste_element');
        $this->addSql('CREATE TABLE liste_element (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, liste_id INTEGER NOT NULL, creator_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL COLLATE BINARY, img VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, info VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO liste_element (id, liste_id, creator_id, name, description, img, date, info) SELECT id, liste_id, creator_id, name, description, img, date, info FROM __temp__liste_element');
        $this->addSql('DROP TABLE __temp__liste_element');
        $this->addSql('CREATE INDEX IDX_F07AC668E85441D8 ON liste_element (liste_id)');
        $this->addSql('CREATE INDEX IDX_F07AC66861220EA6 ON liste_element (creator_id)');
        $this->addSql('DROP INDEX IDX_5A10856461220EA6');
        $this->addSql('DROP INDEX IDX_5A1085641F1F2A24');
        $this->addSql('CREATE TEMPORARY TABLE __temp__vote AS SELECT id, creator_id, element_id, value, type FROM vote');
        $this->addSql('DROP TABLE vote');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, element_id INTEGER DEFAULT NULL, value INTEGER NOT NULL, type VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO vote (id, creator_id, element_id, value, type) SELECT id, creator_id, element_id, value, type FROM __temp__vote');
        $this->addSql('DROP TABLE __temp__vote');
        $this->addSql('CREATE INDEX IDX_5A10856461220EA6 ON vote (creator_id)');
        $this->addSql('CREATE INDEX IDX_5A1085641F1F2A24 ON vote (element_id)');
    }
}
