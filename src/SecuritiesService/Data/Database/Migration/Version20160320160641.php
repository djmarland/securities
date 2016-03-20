<?php

namespace SecuritiesService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160320160641 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE countries ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE currencies ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE industries ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE parent_groups ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE products ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE regions ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sectors ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE securities ADD uuid VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE yield_curves ADD uuid VARCHAR(255) NOT NULL');

        $this->addSql('UPDATE companies set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE countries set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE currencies set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE industries set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE parent_groups set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE products set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE regions set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE sectors set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE securities set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
        $this->addSql('UPDATE yield_curves set uuid = LOWER(insert(insert(insert(insert(hex(id),9,0,\'-\'),14,0,\'-\'),19,0,\'-\'),24,0,\'-\'))');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies DROP uuid');
        $this->addSql('ALTER TABLE countries DROP uuid');
        $this->addSql('ALTER TABLE currencies DROP uuid');
        $this->addSql('ALTER TABLE industries DROP uuid');
        $this->addSql('ALTER TABLE parent_groups DROP uuid');
        $this->addSql('ALTER TABLE products DROP uuid');
        $this->addSql('ALTER TABLE regions DROP uuid');
        $this->addSql('ALTER TABLE sectors DROP uuid');
        $this->addSql('ALTER TABLE securities DROP uuid');
        $this->addSql('ALTER TABLE yield_curves DROP uuid');
    }
}
