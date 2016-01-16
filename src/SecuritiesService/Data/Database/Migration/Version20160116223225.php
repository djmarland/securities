<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160116223225 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE yield_curves (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, parent_group_id INT DEFAULT NULL, year INT NOT NULL, type VARCHAR(255) NOT NULL, data_points VARCHAR(1000) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1A948EF638248176 (currency_id), INDEX IDX_1A948EF661997596 (parent_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
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

        $this->addSql('DROP TABLE yield_curves');
    }
}
