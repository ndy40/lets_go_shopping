<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200822104450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, channel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_3DC1A4597E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list_shopping_item (shopping_list_id INT NOT NULL, shopping_item_id INT NOT NULL, INDEX IDX_BB4D9BBC23245BF9 (shopping_list_id), INDEX IDX_BB4D9BBCCE51F2C (shopping_item_id), PRIMARY KEY(shopping_list_id, shopping_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A4597E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE shopping_list_shopping_item ADD CONSTRAINT FK_BB4D9BBC23245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shopping_list_shopping_item ADD CONSTRAINT FK_BB4D9BBCCE51F2C FOREIGN KEY (shopping_item_id) REFERENCES shopping_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users ADD collaborators_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E934F7BA2 FOREIGN KEY (collaborators_id) REFERENCES shopping_list (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E934F7BA2 ON users (collaborators_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_list_shopping_item DROP FOREIGN KEY FK_BB4D9BBC23245BF9');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E934F7BA2');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE shopping_list_shopping_item');
        $this->addSql('DROP INDEX IDX_1483A5E934F7BA2 ON users');
        $this->addSql('ALTER TABLE users DROP collaborators_id');
    }
}
