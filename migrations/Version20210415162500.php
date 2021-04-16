<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415162500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_E6D6DD752B36786B6DE440268CDE57292D5B0234 ON announce');
        $this->addSql('CREATE FULLTEXT INDEX IDX_E6D6DD752B36786B6DE44026 ON announce (title, description)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_E6D6DD752B36786B6DE44026 ON announce');
        $this->addSql('CREATE FULLTEXT INDEX IDX_E6D6DD752B36786B6DE440268CDE57292D5B0234 ON announce (title, description, type, city)');
    }
}
