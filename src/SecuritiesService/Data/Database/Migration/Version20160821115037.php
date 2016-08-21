<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160821115037 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE indices (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_index (security_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', index_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', INDEX IDX_6CCF84AB6DBE4214 (security_id), INDEX IDX_6CCF84AB84337261 (index_id), PRIMARY KEY(security_id, index_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_types (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE security_index ADD CONSTRAINT FK_6CCF84AB6DBE4214 FOREIGN KEY (security_id) REFERENCES securities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE security_index ADD CONSTRAINT FK_6CCF84AB84337261 FOREIGN KEY (index_id) REFERENCES indices (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE securities ADD type_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE securities ADD CONSTRAINT FK_A8210B24C54C8C93 FOREIGN KEY (type_id) REFERENCES security_types (id)');
        $this->addSql('CREATE INDEX IDX_A8210B24C54C8C93 ON securities (type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE security_index DROP FOREIGN KEY FK_6CCF84AB84337261');
        $this->addSql('ALTER TABLE securities DROP FOREIGN KEY FK_A8210B24C54C8C93');
        $this->addSql('DROP TABLE indices');
        $this->addSql('DROP TABLE security_index');
        $this->addSql('DROP TABLE security_types');
        $this->addSql('DROP INDEX IDX_A8210B24C54C8C93 ON securities');
        $this->addSql('ALTER TABLE securities DROP type_id');
    }
}
