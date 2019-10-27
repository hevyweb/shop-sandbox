<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190912171556 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create a table for remember me token.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `rememberme_token` (
            `series` CHAR (88) UNIQUE NOT NULL,
            `value` CHAR (88) NOT NULL,
            `lastUsed` DATETIME NOT NULL,
            `class` VARCHAR (100) NOT NULL,
            `username` VARCHAR (200) NOT NULL
            ) ENGINE = InnoDB;
SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS `rememberme_token`');
    }
}
