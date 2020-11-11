<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201028151454 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_member_invitations (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, invited_at DATETIME NOT NULL, responded_at DATETIME DEFAULT NULL, uuid_value VARCHAR(191) NOT NULL, username_value VARCHAR(191) NOT NULL, status_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_4C48FA0DDF0E665D (uuid_value), INDEX IDX_4C48FA0D166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_member_invitations ADD CONSTRAINT FK_4C48FA0D166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE project_member_invitations');
    }
}
