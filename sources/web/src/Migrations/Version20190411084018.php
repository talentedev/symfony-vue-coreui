<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190411084018 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE49D3FD2');
        $this->addSql('DROP INDEX IDX_D34A04ADE49D3FD2 ON product');
        $this->addSql('ALTER TABLE product ADD status VARCHAR(255) NOT NULL, DROP capacity, CHANGE compnay_id company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD979B1AD6 ON product (company_id)');
        $this->addSql('ALTER TABLE production ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E04584665A ON production (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD979B1AD6');
        $this->addSql('DROP INDEX IDX_D34A04AD979B1AD6 ON product');
        $this->addSql('ALTER TABLE product ADD capacity INT NOT NULL, DROP status, CHANGE company_id compnay_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE49D3FD2 FOREIGN KEY (compnay_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADE49D3FD2 ON product (compnay_id)');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E04584665A');
        $this->addSql('DROP INDEX IDX_D3EDB1E04584665A ON production');
        $this->addSql('ALTER TABLE production DROP product_id');
    }
}
