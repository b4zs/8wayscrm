<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170827190132 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classification__category DROP FOREIGN KEY FK_43629B366B00C1CF');
        $this->addSql('ALTER TABLE classification__context CHANGE id id VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE classification__category CHANGE context_id context_id VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE classification__category ADD CONSTRAINT FK_43629B366B00C1CF FOREIGN KEY (context_id) REFERENCES classification__context (id);');
        $this->addSql("INSERT INTO `classification__context` (`id`, `name`, `enabled`, `created_at`, `updated_at`) VALUES ('default', 'default', 1, '2017-08-27 21:39:41', '2017-08-27 21:39:41');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classification__category CHANGE context_id context_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classification__context CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
