<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019171241 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, uuid_value VARCHAR(191) NOT NULL, project_title_value VARCHAR(191) NOT NULL, slug_value VARCHAR(191) NOT NULL, project_owner_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_5C93B3A4DF0E665D (uuid_value), UNIQUE INDEX UNIQ_5C93B3A4C88FDCC9 (slug_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE projects');
    }
}
