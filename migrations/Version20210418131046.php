<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418131046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD sender_user_id INT DEFAULT NULL, ADD sender_admin_id INT DEFAULT NULL, ADD email_sender VARCHAR(255) DEFAULT NULL, DROP sender, CHANGE firstname_sender firstname_sender VARCHAR(128) DEFAULT NULL, CHANGE lastname_sender lastname_sender VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F68106FEE FOREIGN KEY (sender_admin_id) REFERENCES admin (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F2A98155E ON message (sender_user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F68106FEE ON message (sender_admin_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F2A98155E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F68106FEE');
        $this->addSql('DROP INDEX IDX_B6BD307F2A98155E ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F68106FEE ON message');
        $this->addSql('ALTER TABLE message ADD sender VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP sender_user_id, DROP sender_admin_id, DROP email_sender, CHANGE firstname_sender firstname_sender VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE lastname_sender lastname_sender VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
