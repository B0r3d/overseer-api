<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201220192752 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_keys CHANGE project_id project_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_4C48FA0DDF0E665D ON project_member_invitations');
        $this->addSql('ALTER TABLE project_member_invitations DROP uuid_value, CHANGE id id VARCHAR(255) NOT NULL, CHANGE project_id project_id VARCHAR(255) DEFAULT NULL, CHANGE status_value status_value VARCHAR(40) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D3BEDE9ADF0E665D ON project_members');
        $this->addSql('ALTER TABLE project_members DROP uuid_value, CHANGE id id VARCHAR(255) NOT NULL, CHANGE project_id project_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4DF0E665D ON projects');
        $this->addSql('ALTER TABLE projects ADD project_owner_id VARCHAR(255) DEFAULT NULL, DROP uuid_value, DROP project_owner_value, CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A44372EA22 FOREIGN KEY (project_owner_id) REFERENCES project_members (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A44372EA22 ON projects (project_owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_keys CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_member_invitations ADD uuid_value VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE project_id project_id INT DEFAULT NULL, CHANGE status_value status_value VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C48FA0DDF0E665D ON project_member_invitations (uuid_value)');
        $this->addSql('ALTER TABLE project_members ADD uuid_value VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D3BEDE9ADF0E665D ON project_members (uuid_value)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A44372EA22');
        $this->addSql('DROP INDEX UNIQ_5C93B3A44372EA22 ON projects');
        $this->addSql('ALTER TABLE projects ADD uuid_value VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD project_owner_value VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP project_owner_id, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4DF0E665D ON projects (uuid_value)');
    }
}
