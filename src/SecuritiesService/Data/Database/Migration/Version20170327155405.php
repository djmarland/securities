<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170327155405 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE lse_announcements SET status = 0 WHERE status = "new"');
        $this->addSql('UPDATE lse_announcements SET status = 20 WHERE status = "error"');
        $this->addSql('UPDATE lse_announcements SET status = -10 WHERE status = "done"');
        $this->addSql('UPDATE lse_announcements SET status = -20 WHERE status = "low"');
        $this->addSql('ALTER TABLE lse_announcements CHANGE status status INT NOT NULL');

        $this->addSql('UPDATE lse_announcements SET status = -20 WHERE status = 0 AND title NOT LIKE "Admission%"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lse_announcements CHANGE status status VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('UPDATE lse_announcements SET status = "new" WHERE status = "0"');
        $this->addSql('UPDATE lse_announcements SET status = "error" WHERE status = "20"');
        $this->addSql('UPDATE lse_announcements SET status = "done" WHERE status = "-10"');
        $this->addSql('UPDATE lse_announcements SET status = "low" WHERE status = "-20"');
    }
}
