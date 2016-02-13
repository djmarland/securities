<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160213195351 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE industries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sectors (id INT AUTO_INCREMENT NOT NULL, industry_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B59406982B19A734 (industry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sectors ADD CONSTRAINT FK_B59406982B19A734 FOREIGN KEY (industry_id) REFERENCES industries (id)');
        $this->addSql('ALTER TABLE parent_groups ADD sector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parent_groups ADD CONSTRAINT FK_BF7EFAB6DE95C867 FOREIGN KEY (sector_id) REFERENCES sectors (id)');
        $this->addSql('CREATE INDEX IDX_BF7EFAB6DE95C867 ON parent_groups (sector_id)');
        $this->addSql('ALTER TABLE yield_curves CHANGE data_points data_points VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sectors DROP FOREIGN KEY FK_B59406982B19A734');
        $this->addSql('ALTER TABLE parent_groups DROP FOREIGN KEY FK_BF7EFAB6DE95C867');
        $this->addSql('DROP TABLE industries');
        $this->addSql('DROP TABLE sectors');
        $this->addSql('DROP INDEX IDX_BF7EFAB6DE95C867 ON parent_groups');
        $this->addSql('ALTER TABLE parent_groups DROP sector_id');
        $this->addSql('ALTER TABLE yield_curves CHANGE data_points data_points VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci');
    }
}
