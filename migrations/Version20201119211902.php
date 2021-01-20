<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201119211902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_member_invitations (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, invited_at DATETIME NOT NULL, responded_at DATETIME DEFAULT NULL, uuid_value VARCHAR(191) NOT NULL, username_value VARCHAR(191) NOT NULL, status_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_4C48FA0DDF0E665D (uuid_value), INDEX IDX_4C48FA0D166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_members (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, joined_at DATETIME NOT NULL, uuid_value VARCHAR(191) NOT NULL, username_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_D3BEDE9ADF0E665D (uuid_value), INDEX IDX_D3BEDE9A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, uuid_value VARCHAR(191) NOT NULL, project_title_value VARCHAR(191) NOT NULL, slug_value VARCHAR(191) NOT NULL, project_owner_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_5C93B3A4DF0E665D (uuid_value), UNIQUE INDEX UNIQ_5C93B3A4C88FDCC9 (slug_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sessions (id VARCHAR(191) NOT NULL, user_id VARCHAR(191) DEFAULT NULL, session_start DATETIME NOT NULL, refresh_token VARCHAR(191) NOT NULL, expiry_date_value DATETIME NOT NULL, status_value VARCHAR(20) NOT NULL, INDEX IDX_9A609D13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id VARCHAR(191) NOT NULL, created_at DATETIME NOT NULL, username_value VARCHAR(191) NOT NULL, email_value VARCHAR(191) NOT NULL, password_value VARCHAR(191) NOT NULL, roles_array LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_1483A5E99E2D2688 (username_value), UNIQUE INDEX UNIQ_1483A5E9803A19BB (email_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_member_invitations ADD CONSTRAINT FK_4C48FA0D166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_member_invitations DROP FOREIGN KEY FK_4C48FA0D166D1F9C');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9A166D1F9C');
        $this->addSql('ALTER TABLE sessions DROP FOREIGN KEY FK_9A609D13A76ED395');
        $this->addSql('DROP TABLE project_member_invitations');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE users');
    }
}
