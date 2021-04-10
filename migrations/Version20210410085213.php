<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210410085213 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, owner_id INT NOT NULL, title VARCHAR(128) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(128) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(128) NOT NULL, area INT NOT NULL, price INT NOT NULL, rooms SMALLINT NOT NULL, bedrooms SMALLINT NOT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, firstpage TINYINT(1) NOT NULL, INDEX IDX_E6D6DD75B03A8386 (created_by_id), INDEX IDX_E6D6DD757E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD75B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD757E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE announce');
    }
}
