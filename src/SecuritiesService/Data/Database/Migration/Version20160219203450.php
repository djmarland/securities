<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160219203450 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE companies (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', country_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', parent_group_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8244AA3AF92F3E70 (country_id), INDEX IDX_8244AA3A61997596 (parent_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE countries (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', region_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5D66EBAD98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currencies (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE industries (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parent_groups (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', sector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BF7EFAB6DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', number INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sectors (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', industry_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B59406982B19A734 (industry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE securities (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', product_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', company_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', currency_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, isin VARCHAR(12) NOT NULL, money_raised DOUBLE PRECISION NOT NULL, start_date DATE NOT NULL, maturity_date DATE DEFAULT NULL, coupon DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A8210B242FE82D2D (isin), INDEX IDX_A8210B244584665A (product_id), INDEX IDX_A8210B24979B1AD6 (company_id), INDEX IDX_A8210B2438248176 (currency_id), INDEX isin_idx (isin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yield_curves (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', currency_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', parent_group_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', year INT NOT NULL, type VARCHAR(255) NOT NULL, data_points TEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1A948EF638248176 (currency_id), INDEX IDX_1A948EF661997596 (parent_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3AF92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)');
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3A61997596 FOREIGN KEY (parent_group_id) REFERENCES parent_groups (id)');
        $this->addSql('ALTER TABLE countries ADD CONSTRAINT FK_5D66EBAD98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE parent_groups ADD CONSTRAINT FK_BF7EFAB6DE95C867 FOREIGN KEY (sector_id) REFERENCES sectors (id)');
        $this->addSql('ALTER TABLE sectors ADD CONSTRAINT FK_B59406982B19A734 FOREIGN KEY (industry_id) REFERENCES industries (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B244584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B2438248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE yield_curves ADD CONSTRAINT FK_1A948EF638248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE yield_curves ADD CONSTRAINT FK_1A948EF661997596 FOREIGN KEY (parent_group_id) REFERENCES parent_groups (id)');
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
        $this->addSql('ALTER TABLE yield_curves DROP FOREIGN KEY FK_1A948EF638248176');
        $this->addSql('ALTER TABLE sectors DROP FOREIGN KEY FK_B59406982B19A734');
        $this->addSql('ALTER TABLE companies DROP FOREIGN KEY FK_8244AA3A61997596');
        $this->addSql('ALTER TABLE yield_curves DROP FOREIGN KEY FK_1A948EF661997596');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B244584665A');
        $this->addSql('ALTER TABLE countries DROP FOREIGN KEY FK_5D66EBAD98260155');
        $this->addSql('ALTER TABLE parent_groups DROP FOREIGN KEY FK_BF7EFAB6DE95C867');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE industries');
        $this->addSql('DROP TABLE parent_groups');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE sectors');
        $this->addSql('DROP TABLE securities');
        $this->addSql('DROP TABLE yield_curves');
    }
}
