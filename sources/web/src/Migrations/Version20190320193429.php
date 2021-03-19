<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320193429 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, report_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, upload_date DATETIME NOT NULL, file VARCHAR(255) NOT NULL, INDEX IDX_C42F7784A35E36EA (report_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_sub_category (id INT AUTO_INCREMENT NOT NULL, report_category_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B2896701A35E36EA (report_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A35E36EA FOREIGN KEY (report_category_id) REFERENCES report_category (id)');
        $this->addSql('ALTER TABLE report_sub_category ADD CONSTRAINT FK_B2896701A35E36EA FOREIGN KEY (report_category_id) REFERENCES report_category (id)');
        $this->addSql('ALTER TABLE roles CHANGE code code VARCHAR(30) NOT NULL');

        $this->addSql("INSERT INTO report_category (id, name)
                            VALUES
                            ('1', 'Events'),
                            ('2', 'Market Research'),
                            ('3', 'Committees'),
                            ('4', 'Health, Safety & Environment (HSE)'),
                            ('5', 'Regulatory Affairs');");

        $this->addSql("INSERT INTO report_sub_category (report_category_id, name)
                            VALUES
                            ('1', 'Web Annual Conference'),
                            ('1', 'Web EPD Conference'),
                            ('1', 'Web Other Conferences'),
                            ('1', 'Web China Banquet'),
                            ('1', 'Web Workshop'),
                            ('1', 'Other Conferences'),
                            ('2', 'Trade matrices'),
                            ('2', 'Trade duties'),
                            ('2', 'Mn ore & alloy'),
                            ('2', 'Mn metal'),
                            ('2', 'Rest of the world weekly report'),
                            ('2', 'China weekly report'),
                            ('2', 'Mn directory'),
                            ('2', 'Steel production'),
                            ('2', 'China report'),
                            ('2', 'Special studies & reports'),
                            ('2', 'EMD production'),
                            ('2', 'Maps of Mn producers'),
                            ('2', 'Monthly report'),
                            ('2', 'Annual report'),
                            ('3', 'Statistics Committee'),
                            ('3', 'HSE Committee'),
                            ('3', 'China Committee'),
                            ('3', 'EPD Committee'),
                            ('3', 'Regulatory Committee'),
                            ('3', 'Technical'),
                            ('4', 'HSE Newsletter'),
                            ('4', 'Completed Projects'),
                            ('4', 'Current Projects'),
                            ('4', 'Safety datasheet (SDS)'),
                            ('4', 'Factsheets'),
                            ('4', 'E-Library'),
                            ('4', 'Lexicon'),
                            ('5', 'Asian Regulatory Review'),
                            ('5', 'Regulatory Affairs Newsletter'),
                            ('5', 'REACH'),
                            ('5', 'Completed Projects'),
                            ('5', 'Current Projects');");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A35E36EA');
        $this->addSql('ALTER TABLE report_sub_category DROP FOREIGN KEY FK_B2896701A35E36EA');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE report_category');
        $this->addSql('DROP TABLE report_sub_category');
        $this->addSql('ALTER TABLE roles CHANGE code code VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
