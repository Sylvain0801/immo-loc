<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414090308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce ADD owner_id INT DEFAULT NULL, ADD tenant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD757E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD759033212A FOREIGN KEY (tenant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E6D6DD757E3C61F9 ON announce (owner_id)');
        $this->addSql('CREATE INDEX IDX_E6D6DD759033212A ON announce (tenant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD757E3C61F9');
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD759033212A');
        $this->addSql('DROP INDEX IDX_E6D6DD757E3C61F9 ON announce');
        $this->addSql('DROP INDEX IDX_E6D6DD759033212A ON announce');
        $this->addSql('ALTER TABLE announce DROP owner_id, DROP tenant_id');
    }
}
