<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191216161717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds 2 tables "order" and "order_item". We need to products because order items is only a link 
        to product but there can be several products and we have to combine them in one item.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql( "
        CREATE TABLE `order` (
            `id` INT AUTO_INCREMENT NOT NULL, 
            `created_at` DATETIME NOT NULL, 
            `status` INT NOT NULL, 
            `completed_at` DATETIME DEFAULT NULL, 
            PRIMARY KEY(`id`)
        ) DEFAULT CHARACTER SET utf8mb4 
        COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"
        );

        $this->addSql( "
        CREATE TABLE `order_item` (
            `id` INT AUTO_INCREMENT NOT NULL, 
            `order_id` INT NOT NULL, 
            `product_id` INT NOT NULL, 
            `price` DOUBLE PRECISION NOT NULL, 
            `quantity` INT NOT NULL, 
            INDEX IDX_52EA1F09FCDAEAAA (`order_id`), 
            INDEX IDX_FS345DFGSWF2324F (`product_id`), 
            PRIMARY KEY(`id`)
        ) DEFAULT CHARACTER SET utf8mb4 
        COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"
        );

        $this->addSql( "
        ALTER TABLE `order_item` 
        ADD CONSTRAINT FK_52EA1F09FCDAEAAA 
        FOREIGN KEY (`order_id`) 
        REFERENCES `order` (`id`);
        
        ALTER TABLE `order_item` 
        ADD CONSTRAINT IDX_FS345DFGSWF2324F 
        FOREIGN KEY (`product_id`) 
        REFERENCES `product` (`id`)"
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order_item` DROP FOREIGN KEY FK_52EA1F09FCDAEAAA');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
    }
}
