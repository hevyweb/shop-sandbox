<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190910155325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create table for categories';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('
            CREATE TABLE IF NOT EXISTS `category` (
                `id` INT AUTO_INCREMENT NOT NULL, 
                `parent_id` INT DEFAULT NULL, 
                `created_by_id` INT NOT NULL, 
                `updated_by_id` INT NOT NULL, 
                `name` VARCHAR(255) NOT NULL, 
                `created` DATETIME NOT NULL, 
                `updated` DATETIME NOT NULL,
                PRIMARY KEY(`id`)
            ) ENGINE = InnoDB'
        );

        $this->addSql('
            ALTER TABLE `category` ADD CONSTRAINT IDX_64C19C1727ACA70 FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`);
            ALTER TABLE `category` ADD CONSTRAINT IDX_64C19C1B03A8386 FOREIGN KEY (`created_by_id`) REFERENCES user (`id`);
            ALTER TABLE `category` ADD CONSTRAINT IDX_64C19C1896DBBDE FOREIGN KEY (`updated_by_id`) REFERENCES user (`id`);
            ALTER TABLE `category` ADD UNIQUE KEY `UNIQ_64C19C15E237E06` (`name`)'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS `category`');
    }
}
