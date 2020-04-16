<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190909162959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create a table for remember me token.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE rememberme_token (
            id INT AUTO_INCREMENT NOT NULL, 
            series VARCHAR(88) NOT NULL, 
            value VARCHAR(88) NOT NULL, 
            last_used DATETIME NOT NULL, 
            class VARCHAR(100) NOT NULL, 
            username VARCHAR(200) NOT NULL, 
            UNIQUE INDEX UNIQ_4C4CEC2C3A10012D (series), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS `rememberme_token`');
    }
}
