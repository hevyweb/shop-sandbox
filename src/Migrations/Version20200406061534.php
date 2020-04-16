<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406061534 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE `role` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(69) NOT NULL,
          `label` varchar(64) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $this->addSql('ALTER TABLE `role`
          ADD UNIQUE KEY `UNIQ_57698A6A77153098` (`code`);
        COMMIT');

        $this->addSql('CREATE TABLE `user_role` (
            `role_id` int(11) NOT NULL, 
            `user_id` int(11) NOT NULL, 
            INDEX IDX_2DE8C6A3A76ED395 (`user_id`), 
            INDEX IDX_2DE8C6A3D60322AC (`role_id`),
            PRIMARY KEY (`user_id`, `role_id`)
            ) DEFAULT CHARACTER SET utf8 
            COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `user_role` ADD CONSTRAINT `FK_role_id2` FOREIGN KEY (`role_id`) REFERENCES role (`id`) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `user_role` ADD CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES user (`id`) ON DELETE CASCADE');
        $this->addSql('INSERT INTO `role` (`id`, `code`, `label`) VALUES (1, "ROLE_USER", "User"), (2, "ROLE_ADMIN", "Administrator")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE `user_role`;');
        $this->addSql('DROP TABLE `role`');
    }
}
