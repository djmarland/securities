<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160727145327 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE exchange_rates (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', currency_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', rate DOUBLE PRECISION NOT NULL, date DATE NOT NULL, uuid VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5AE3E77438248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exchange_rates ADD CONSTRAINT FK_5AE3E77438248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE companies ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE config ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE countries ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE currencies ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE industries ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE parent_groups ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE products ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE regions ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sectors ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE securities ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE yield_curves ADD updated_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE exchange_rates');
        $this->addSql('ALTER TABLE companies DROP updated_at');
        $this->addSql('ALTER TABLE config DROP updated_at');
        $this->addSql('ALTER TABLE countries DROP updated_at');
        $this->addSql('ALTER TABLE currencies DROP updated_at');
        $this->addSql('ALTER TABLE industries DROP updated_at');
        $this->addSql('ALTER TABLE parent_groups DROP updated_at');
        $this->addSql('ALTER TABLE products DROP updated_at');
        $this->addSql('ALTER TABLE regions DROP updated_at');
        $this->addSql('ALTER TABLE sectors DROP updated_at');
        $this->addSql('ALTER TABLE securities DROP updated_at');
        $this->addSql('ALTER TABLE users DROP updated_at');
        $this->addSql('ALTER TABLE yield_curves DROP updated_at');
    }
}
