<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190909162345 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user table and admin user with login and password: admin';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `first_name` varchar(255) NOT NULL,
            `last_name` varchar(255) NOT NULL,
            `age` int(11),
            `sex` varchar(1),
            `username` varchar(64) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(64) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB'
        );

        $this->addSql("
            ALTER TABLE `user`
            ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
            ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)"
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS `user`');
    }
}
