<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160615085505 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies ADD market_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE securities ADD market VARCHAR(255) DEFAULT NULL, ADD coupon_type VARCHAR(255) DEFAULT NULL, ADD margin DOUBLE PRECISION DEFAULT NULL, CHANGE money_raised money_raised DOUBLE PRECISION DEFAULT NULL, CHANGE start_date start_date DATE DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies DROP market_code');
        $this->addSql('ALTER TABLE securities DROP market, DROP coupon_type, DROP margin, CHANGE money_raised money_raised DOUBLE PRECISION NOT NULL, CHANGE start_date start_date DATE NOT NULL');
    }
}
