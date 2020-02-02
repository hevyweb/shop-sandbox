<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191126112907 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'I created 2 tables. Table "product" has products. Table "product_category" shows relation between
        products and categories. One product can be in several categories.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
          CREATE TABLE product (
            id INT AUTO_INCREMENT NOT NULL, 
            created_by_id INT NOT NULL, 
            updated_by_id INT DEFAULT NULL, 
            name VARCHAR(255) NOT NULL, 
            description LONGTEXT DEFAULT NULL, 
            image VARCHAR(255) DEFAULT NULL, 
            enabled TINYINT(1) NOT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME DEFAULT NULL, 
            price DOUBLE PRECISION DEFAULT NULL, 
            INDEX IDX_D34A04ADB03A8386 (created_by_id), 
            INDEX IDX_D34A04AD896DBBDE (updated_by_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );

        $this->addSql('
          CREATE TABLE product_category (
            product_id INT NOT NULL, 
            category_id INT NOT NULL, 
            INDEX IDX_CDFC73564584665A (product_id), 
            INDEX IDX_CDFC735612469DE2 (category_id), 
            PRIMARY KEY(product_id, category_id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );

        $this->addSql('
          ALTER TABLE product 
          ADD CONSTRAINT FK_D34A04ADB03A8386 
          FOREIGN KEY (created_by_id) REFERENCES user (id)'
        );

        $this->addSql('
          ALTER TABLE product 
          ADD CONSTRAINT FK_D34A04AD896DBBDE 
          FOREIGN KEY (updated_by_id) 
          REFERENCES user (id)'
        );

        $this->addSql('
          ALTER TABLE product_category 
          ADD CONSTRAINT FK_CDFC73564584665A 
          FOREIGN KEY (product_id) 
          REFERENCES product (id) 
          ON DELETE CASCADE'
        );

        $this->addSql('
          ALTER TABLE product_category 
          ADD CONSTRAINT FK_CDFC735612469DE2 
          FOREIGN KEY (category_id) 
          REFERENCES category (id) 
          ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73564584665A');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE product');
    }
}
