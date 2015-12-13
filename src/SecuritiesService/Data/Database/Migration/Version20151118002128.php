<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151118002128 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE companies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE countries (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5D66EBAD98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currencies (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fsa04748 (id INT AUTO_INCREMENT NOT NULL, line INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fsa_buckets (id INT AUTO_INCREMENT NOT NULL, years_from DOUBLE PRECISION NOT NULL, years_to DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE markets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE market_sector (id INT AUTO_INCREMENT NOT NULL, sector_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE market_segments (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securities (id INT AUTO_INCREMENT NOT NULL, fsa04748_id INT DEFAULT NULL, fsa_contractual_bucket_id INT DEFAULT NULL, fsa_residual_bucket_id INT DEFAULT NULL, market_id INT DEFAULT NULL, company_id INT DEFAULT NULL, security_type_id INT DEFAULT NULL, market_segment_id INT DEFAULT NULL, market_sector_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, isin VARCHAR(12) NOT NULL, tidm VARCHAR(4) NOT NULL, money_raised DOUBLE PRECISION NOT NULL, start_date DATE NOT NULL, maturity_date DATE DEFAULT NULL, coupon DOUBLE PRECISION DEFAULT NULL, weighting INT DEFAULT NULL, contractual_maturity DOUBLE PRECISION DEFAULT NULL, issue_month INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A8210B242FE82D2D (isin), INDEX IDX_A8210B24BD2071E8 (fsa04748_id), INDEX IDX_A8210B24C0916531 (fsa_contractual_bucket_id), INDEX IDX_A8210B24E43243C0 (fsa_residual_bucket_id), INDEX IDX_A8210B24622F3F37 (market_id), INDEX IDX_A8210B24979B1AD6 (company_id), INDEX IDX_A8210B24A8A7878E (security_type_id), INDEX IDX_A8210B24B5D73EB1 (market_segment_id), INDEX IDX_A8210B244BC733F0 (market_sector_id), INDEX IDX_A8210B2438248176 (currency_id), INDEX IDX_A8210B24F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE countries ADD CONSTRAINT FK_5D66EBAD98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24BD2071E8 FOREIGN KEY (fsa04748_id) REFERENCES fsa04748 (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24C0916531 FOREIGN KEY (fsa_contractual_bucket_id) REFERENCES fsa_buckets (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24E43243C0 FOREIGN KEY (fsa_residual_bucket_id) REFERENCES fsa_buckets (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24622F3F37 FOREIGN KEY (market_id) REFERENCES markets (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24A8A7878E FOREIGN KEY (security_type_id) REFERENCES security_types (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24B5D73EB1 FOREIGN KEY (market_segment_id) REFERENCES market_segments (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B244BC733F0 FOREIGN KEY (market_sector_id) REFERENCES market_sector (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B2438248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24979B1AD6');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24F92F3E70');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B2438248176');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24BD2071E8');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24C0916531');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24E43243C0');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24622F3F37');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B244BC733F0');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24B5D73EB1');
        $this->addSql('ALTER TABLE countries DROP FOREIGN KEY FK_5D66EBAD98260155');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24A8A7878E');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE fsa04748');
        $this->addSql('DROP TABLE fsa_buckets');
        $this->addSql('DROP TABLE markets');
        $this->addSql('DROP TABLE market_sector');
        $this->addSql('DROP TABLE market_segments');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE securities');
        $this->addSql('DROP TABLE security_types');
    }
}
