<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125064338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, document VARCHAR(255) NOT NULL, names VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, cellphone VARCHAR(255) NOT NULL, auth0_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C82E74D8698A76 (document), UNIQUE INDEX UNIQ_C82E74E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_sessions (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, session_id VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, confirmed TINYINT(1) NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_DAA6DF7B19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_EAA81A4C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallets (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, balance DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_967AAA6C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_sessions ADD CONSTRAINT FK_DAA6DF7B19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE wallets ADD CONSTRAINT FK_967AAA6C19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_sessions DROP FOREIGN KEY FK_DAA6DF7B19EB6921');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C19EB6921');
        $this->addSql('ALTER TABLE wallets DROP FOREIGN KEY FK_967AAA6C19EB6921');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE payment_sessions');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE wallets');
    }
}
