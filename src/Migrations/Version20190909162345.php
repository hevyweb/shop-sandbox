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
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `user` (
            `id` int(11) NOT NULL,
            `first_name` varchar(255) NOT NULL,
            `last_name` varchar(255) NOT NULL,
            `age` int(11),
            `sex` varchar(1),
            `username` varchar(64) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(64) NOT NULL,
            `roles` longtext NOT NULL COMMENT '(DC2Type:array)'
            ) ENGINE=InnoDB
SQL;

        $this->addSql($sql);

        $sql = <<<SQL
            ALTER TABLE `user`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
SQL;

        $this->addSql($sql);

        //Create admin user

        $sql = <<<SQL
          INSERT INTO `user` (`id`, `first_name`, `last_name`, `age`, `sex`, `username`, `email`, `password`, `roles`) 
          VALUES
          (2, 'Admin', 'Admin', 81, '1', 'admin', 'nosend@gmail.com', '$2y$13$mCd2dq6lxJ7tlNUIptUr..HXU1ILNj/HsbgkNZiKes2/L4Qv8OE9y', 'a:0:{}')
          
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS user');
    }
}
