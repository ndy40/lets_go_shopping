<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200627174741 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP(), ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP()');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_8D93D649E7927C74');
    }
}
