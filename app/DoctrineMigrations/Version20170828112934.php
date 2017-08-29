<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170828112934 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_action_question (question_action_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_A5A9530594E7B4AE (question_action_id), INDEX IDX_A5A953051E27F6BF (question_id), PRIMARY KEY(question_action_id, question_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_action_tag (question_action_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_59A7B64B94E7B4AE (question_action_id), INDEX IDX_59A7B64BBAD26311 (tag_id), PRIMARY KEY(question_action_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_action_question_group (question_action_id INT NOT NULL, question_group_id INT NOT NULL, INDEX IDX_275645F594E7B4AE (question_action_id), INDEX IDX_275645F59D5C694B (question_group_id), PRIMARY KEY(question_action_id, question_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_action_question ADD CONSTRAINT FK_A5A9530594E7B4AE FOREIGN KEY (question_action_id) REFERENCES question_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_action_question ADD CONSTRAINT FK_A5A953051E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_action_tag ADD CONSTRAINT FK_59A7B64B94E7B4AE FOREIGN KEY (question_action_id) REFERENCES question_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_action_tag ADD CONSTRAINT FK_59A7B64BBAD26311 FOREIGN KEY (tag_id) REFERENCES classification__tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_action_question_group ADD CONSTRAINT FK_275645F594E7B4AE FOREIGN KEY (question_action_id) REFERENCES question_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_action_question_group ADD CONSTRAINT FK_275645F59D5C694B FOREIGN KEY (question_group_id) REFERENCES question_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE object_identity__index ADD CONSTRAINT FK_A01544A9EFEA8D6C FOREIGN KEY (objectidentity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE question_action DROP FOREIGN KEY FK_4091A39A4847D827');
        $this->addSql('DROP INDEX IDX_4091A39A4847D827 ON question_action');
        $this->addSql('ALTER TABLE question_action DROP implied_question_id');
        $this->addSql('ALTER TABLE object_identity__index ADD CONSTRAINT FK_A01544A9EFEA8D6C FOREIGN KEY (objectidentity_id) REFERENCES object_identity (id);');
        $this->addSql('ALTER TABLE question_action ADD quotation_item_name VARCHAR(255) DEFAULT NULL, ADD quotation_item_price_amount DOUBLE PRECISION DEFAULT NULL, ADD quotation_item_price_currency VARCHAR(3) DEFAULT NULL;');
        $this->addSql('ALTER TABLE question ADD hint LONGTEXT DEFAULT NULL;');
        $this->addSql('ALTER TABLE question_group ADD class VARCHAR(255) DEFAULT NULL;');
//        $this->addSql('');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE question_action_question');
        $this->addSql('DROP TABLE question_action_tag');
        $this->addSql('DROP TABLE question_action_question_group');
        $this->addSql('ALTER TABLE question_action ADD implied_question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_action ADD CONSTRAINT FK_4091A39A4847D827 FOREIGN KEY (implied_question_id) REFERENCES question (id)');
        $this->addSql('CREATE INDEX IDX_4091A39A4847D827 ON question_action (implied_question_id)');
    }
}
