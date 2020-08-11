<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811202033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, uuid_value VARCHAR(191) NOT NULL, username_value VARCHAR(191) NOT NULL, email_value VARCHAR(191) NOT NULL, password_value VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9DF0E665D (uuid_value), UNIQUE INDEX UNIQ_1483A5E99E2D2688 (username_value), UNIQUE INDEX UNIQ_1483A5E9803A19BB (email_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}