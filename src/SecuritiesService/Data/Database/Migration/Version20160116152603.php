<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160116152603 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE companies (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8244AA3AF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE countries (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5D66EBAD98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currencies (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securities (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, company_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, isin VARCHAR(12) NOT NULL, money_raised DOUBLE PRECISION NOT NULL, start_date DATE NOT NULL, maturity_date DATE DEFAULT NULL, coupon DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A8210B242FE82D2D (isin), INDEX IDX_A8210B244584665A (product_id), INDEX IDX_A8210B24979B1AD6 (company_id), INDEX IDX_A8210B2438248176 (currency_id), INDEX isin_idx (isin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3AF92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)');
        $this->addSql('ALTER TABLE countries ADD CONSTRAINT FK_5D66EBAD98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B244584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B2438248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24979B1AD6');
        $this->addSql('ALTER TABLE companies DROP FOREIGN KEY FK_8244AA3AF92F3E70');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B2438248176');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B244584665A');
        $this->addSql('ALTER TABLE countries DROP FOREIGN KEY FK_5D66EBAD98260155');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE securities');
    }
}
