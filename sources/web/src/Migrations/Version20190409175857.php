<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190409175857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4FBF094FFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, compnay_id INT DEFAULT NULL, commodity_id INT DEFAULT NULL, start_date DATETIME NOT NULL, INDEX IDX_D34A04ADE49D3FD2 (compnay_id), INDEX IDX_D34A04ADB4ACC212 (commodity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commodityGroup (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commodity (id INT AUTO_INCREMENT NOT NULL, commodity_group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5E8D2F7482C89CB1 (commodity_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, capacity INT NOT NULL, production INT NOT NULL, inventory INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE49D3FD2 FOREIGN KEY (compnay_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB4ACC212 FOREIGN KEY (commodity_id) REFERENCES commodity (id)');
        $this->addSql('ALTER TABLE commodity ADD CONSTRAINT FK_5E8D2F7482C89CB1 FOREIGN KEY (commodity_group_id) REFERENCES commodityGroup (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE49D3FD2');
        $this->addSql('ALTER TABLE commodity DROP FOREIGN KEY FK_5E8D2F7482C89CB1');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB4ACC212');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE commodityGroup');
        $this->addSql('DROP TABLE commodity');
        $this->addSql('DROP TABLE production');
    }
}
