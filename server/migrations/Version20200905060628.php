<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200905060628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shopping_item (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT DEFAULT 1 NOT NULL, status VARCHAR(255) DEFAULT \'NOT_PICKED\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6612795F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, channel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3DC1A4597E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list_shopping_item (shopping_list_id INT NOT NULL, shopping_item_id INT NOT NULL, INDEX IDX_BB4D9BBC23245BF9 (shopping_list_id), INDEX IDX_BB4D9BBCCE51F2C (shopping_item_id), PRIMARY KEY(shopping_list_id, shopping_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, verify_token VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) DEFAULT \'0\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_item ADD CONSTRAINT FK_6612795F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A4597E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE shopping_list_shopping_item ADD CONSTRAINT FK_BB4D9BBC23245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_list_shopping_item ADD CONSTRAINT FK_BB4D9BBCCE51F2C FOREIGN KEY (shopping_item_id) REFERENCES shopping_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_list_shopping_item DROP FOREIGN KEY FK_BB4D9BBCCE51F2C');
        $this->addSql('ALTER TABLE shopping_list_shopping_item DROP FOREIGN KEY FK_BB4D9BBC23245BF9');
        $this->addSql('ALTER TABLE shopping_item DROP FOREIGN KEY FK_6612795F7E3C61F9');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A4597E3C61F9');
        $this->addSql('DROP TABLE shopping_item');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE shopping_list_shopping_item');
        $this->addSql('DROP TABLE users');
    }
}
