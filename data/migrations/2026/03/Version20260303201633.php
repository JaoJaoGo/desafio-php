<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303201633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ac_n2s (id INT AUTO_INCREMENT NOT NULL, ac_id INT NOT NULL, name VARCHAR(150) NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_ac_n2_ac_id (ac_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ars (id INT AUTO_INCREMENT NOT NULL, ac_n2_id INT NOT NULL, name VARCHAR(150) NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_ar_ac_n2_id (ac_n2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(190) NOT NULL, passwordHash VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ac_n2s ADD CONSTRAINT FK_59A3FDE8D2E3ED2F FOREIGN KEY (ac_id) REFERENCES acs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ars ADD CONSTRAINT FK_625143F7DBC57632 FOREIGN KEY (ac_n2_id) REFERENCES ac_n2s (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ac_n2s DROP FOREIGN KEY FK_59A3FDE8D2E3ED2F');
        $this->addSql('ALTER TABLE ars DROP FOREIGN KEY FK_625143F7DBC57632');
        $this->addSql('DROP TABLE ac_n2s');
        $this->addSql('DROP TABLE acs');
        $this->addSql('DROP TABLE ars');
        $this->addSql('DROP TABLE users');
    }
}
