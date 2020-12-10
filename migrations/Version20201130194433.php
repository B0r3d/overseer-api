<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201130194433 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions CHANGE expiry_date_value expiry_date_value DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE password_reset_token_id password_reset_token_id VARCHAR(191) DEFAULT NULL, CHANGE password_reset_token_expiry_date_value password_reset_token_expiry_date_value DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions CHANGE expiry_date_value expiry_date_value DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE password_reset_token_id password_reset_token_id VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password_reset_token_expiry_date_value password_reset_token_expiry_date_value DATETIME NOT NULL');
    }
}
