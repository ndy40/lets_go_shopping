<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200830024559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE shopping_item ADD CONSTRAINT FK_6612795F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6612795F7E3C61F9 ON shopping_item (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item DROP FOREIGN KEY FK_6612795F7E3C61F9');
        $this->addSql('DROP INDEX IDX_6612795F7E3C61F9 ON shopping_item');
        $this->addSql('ALTER TABLE shopping_item DROP owner_id');
    }
}
