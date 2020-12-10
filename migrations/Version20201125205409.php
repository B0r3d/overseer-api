<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125205409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD password_reset_token_id VARCHAR(191) NOT NULL, ADD password_reset_token_expiry_date_value DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9AA2E96A0 ON users (password_reset_token_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1483A5E9AA2E96A0 ON users');
        $this->addSql('ALTER TABLE users DROP password_reset_token_id, DROP password_reset_token_expiry_date_value');
    }
}
