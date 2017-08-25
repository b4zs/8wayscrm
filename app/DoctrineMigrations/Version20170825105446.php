<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170825105446 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_tag (question_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_339D56FB1E27F6BF (question_id), INDEX IDX_339D56FBBAD26311 (tag_id), PRIMARY KEY(question_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classification__tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FB1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FBBAD26311 FOREIGN KEY (tag_id) REFERENCES classification__tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE object_identity__index ADD CONSTRAINT FK_A01544A9EFEA8D6C FOREIGN KEY (objectidentity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE question_option ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_option ADD CONSTRAINT FK_5DDB2FB8EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)');
        $this->addSql('CREATE INDEX IDX_5DDB2FB8EA9FDD75 ON question_option (media_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_tag DROP FOREIGN KEY FK_339D56FBBAD26311');
        $this->addSql('DROP TABLE question_tag');
        $this->addSql('DROP TABLE classification__tags');
        $this->addSql('ALTER TABLE question_option DROP FOREIGN KEY FK_5DDB2FB8EA9FDD75');
        $this->addSql('DROP INDEX IDX_5DDB2FB8EA9FDD75 ON question_option');
        $this->addSql('ALTER TABLE question_option DROP media_id');
    }
}
