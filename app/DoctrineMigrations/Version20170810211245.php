<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170810211245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_option (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, text LONGTEXT NOT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5DDB2FB81E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fill_out_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, option_id INT DEFAULT NULL, fill_out_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, step INT DEFAULT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_CB8BBD841E27F6BF (question_id), INDEX IDX_CB8BBD84A7C41D6F (option_id), INDEX IDX_CB8BBD84A35765EE (fill_out_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, value LONGTEXT NOT NULL, created_at VARCHAR(255) NOT NULL, INDEX IDX_DD80652D1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, group_id INT DEFAULT NULL, text VARCHAR(255) NOT NULL, form_type VARCHAR(32) NOT NULL, required_user_role VARCHAR(32) DEFAULT NULL, stage INT DEFAULT NULL, created_at DATETIME NOT NULL, deleted_at VARCHAR(255) DEFAULT NULL, INDEX IDX_B6F7494E12469DE2 (category_id), INDEX IDX_B6F7494EFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_action (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, question_option_id INT DEFAULT NULL, implied_question_id INT DEFAULT NULL, criteria VARCHAR(255) NOT NULL, action_type INT NOT NULL, action_params LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', position INT DEFAULT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_4091A39A1E27F6BF (question_id), INDEX IDX_4091A39AAE1159F4 (question_option_id), INDEX IDX_4091A39A4847D827 (implied_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fill_out (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, state LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_option ADD CONSTRAINT FK_5DDB2FB81E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE fill_out_answer ADD CONSTRAINT FK_CB8BBD841E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE fill_out_answer ADD CONSTRAINT FK_CB8BBD84A7C41D6F FOREIGN KEY (option_id) REFERENCES question_option (id)');
        $this->addSql('ALTER TABLE fill_out_answer ADD CONSTRAINT FK_CB8BBD84A35765EE FOREIGN KEY (fill_out_id) REFERENCES fill_out (id)');
        $this->addSql('ALTER TABLE question_answer ADD CONSTRAINT FK_DD80652D1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E12469DE2 FOREIGN KEY (category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EFE54D947 FOREIGN KEY (group_id) REFERENCES question_group (id)');
        $this->addSql('ALTER TABLE question_action ADD CONSTRAINT FK_4091A39A1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question_action ADD CONSTRAINT FK_4091A39AAE1159F4 FOREIGN KEY (question_option_id) REFERENCES question_option (id)');
        $this->addSql('ALTER TABLE question_action ADD CONSTRAINT FK_4091A39A4847D827 FOREIGN KEY (implied_question_id) REFERENCES question (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fill_out_answer DROP FOREIGN KEY FK_CB8BBD84A7C41D6F');
        $this->addSql('ALTER TABLE question_action DROP FOREIGN KEY FK_4091A39AAE1159F4');
        $this->addSql('ALTER TABLE question_option DROP FOREIGN KEY FK_5DDB2FB81E27F6BF');
        $this->addSql('ALTER TABLE fill_out_answer DROP FOREIGN KEY FK_CB8BBD841E27F6BF');
        $this->addSql('ALTER TABLE question_answer DROP FOREIGN KEY FK_DD80652D1E27F6BF');
        $this->addSql('ALTER TABLE question_action DROP FOREIGN KEY FK_4091A39A1E27F6BF');
        $this->addSql('ALTER TABLE question_action DROP FOREIGN KEY FK_4091A39A4847D827');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EFE54D947');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E12469DE2');
        $this->addSql('ALTER TABLE fill_out_answer DROP FOREIGN KEY FK_CB8BBD84A35765EE');
        $this->addSql('DROP TABLE question_option');
        $this->addSql('DROP TABLE fill_out_answer');
        $this->addSql('DROP TABLE question_answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_group');
        $this->addSql('DROP TABLE question_action');
        $this->addSql('DROP TABLE question_category');
        $this->addSql('DROP TABLE fill_out');
    }
}
