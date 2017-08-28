<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170810210240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__invoice (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, client_billing_address_id INT DEFAULT NULL, file_set_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, issuedAt DATETIME DEFAULT NULL, dueDate DATETIME DEFAULT NULL, status INT DEFAULT NULL, createdAt DATETIME NOT NULL, total_amount DOUBLE PRECISION DEFAULT NULL, total_currency VARCHAR(3) DEFAULT NULL, INDEX IDX_5CA9DBCB166D1F9C (project_id), INDEX IDX_5CA9DBCB134AABF4 (client_billing_address_id), INDEX IDX_5CA9DBCBA8EC2BA7 (file_set_id), INDEX IDX_5CA9DBCBB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_work (invoice_id INT NOT NULL, work_id INT NOT NULL, INDEX IDX_518B0AE52989F1FD (invoice_id), INDEX IDX_518B0AE5BB3453DB (work_id), PRIMARY KEY(invoice_id, work_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__invoice_payment (invoice_id INT NOT NULL, payment_id INT NOT NULL, INDEX IDX_54980B422989F1FD (invoice_id), INDEX IDX_54980B424C3A3BB (payment_id), PRIMARY KEY(invoice_id, payment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__work (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, file_set_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, tracker INT DEFAULT NULL, nature INT DEFAULT NULL, status INT DEFAULT NULL, initialEstimatedTime INT DEFAULT NULL, currentlyEstimatedTime INT DEFAULT NULL, deadline DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, hourly_rate_amount DOUBLE PRECISION DEFAULT NULL, hourly_rate_currency VARCHAR(3) DEFAULT NULL, INDEX IDX_FE2611C1166D1F9C (project_id), INDEX IDX_FE2611C1A8EC2BA7 (file_set_id), INDEX IDX_FE2611C1B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_invoice (work_id INT NOT NULL, invoice_id INT NOT NULL, INDEX IDX_45AEAF66BB3453DB (work_id), INDEX IDX_45AEAF662989F1FD (invoice_id), PRIMARY KEY(work_id, invoice_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__work_redmine_ticket (work_id INT NOT NULL, redmine_ticket_id INT NOT NULL, INDEX IDX_FB1DCBEBB3453DB (work_id), INDEX IDX_FB1DCBE7E8AFB09 (redmine_ticket_id), PRIMARY KEY(work_id, redmine_ticket_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__payment (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, settlementDate DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, createdBy INT DEFAULT NULL, amount_amount DOUBLE PRECISION DEFAULT NULL, amount_currency VARCHAR(3) DEFAULT NULL, INDEX IDX_A1E4488219EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__work_schedule (id INT AUTO_INCREMENT NOT NULL, work_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, scheduleDate DATETIME DEFAULT NULL, scheduleDuration DOUBLE PRECISION DEFAULT NULL, createdAt DATETIME NOT NULL, INDEX IDX_E8A006EABB3453DB (work_id), INDEX IDX_E8A006EAA76ED395 (user_id), INDEX IDX_E8A006EAB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__work_schedule_redmine_ticket (work_schedule_id INT NOT NULL, redmine_ticket_id INT NOT NULL, INDEX IDX_B95560A4BBCA2216 (work_schedule_id), INDEX IDX_B95560A47E8AFB09 (redmine_ticket_id), PRIMARY KEY(work_schedule_id, redmine_ticket_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__spent_time (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, work_id INT DEFAULT NULL, user_id INT DEFAULT NULL, redmine_time_entry_id INT DEFAULT NULL, startDate DATETIME DEFAULT NULL, duration DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, INDEX IDX_36011663166D1F9C (project_id), INDEX IDX_36011663BB3453DB (work_id), INDEX IDX_36011663A76ED395 (user_id), INDEX IDX_360116635766EA4 (redmine_time_entry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounting__spent_time_redmine_ticket (spent_time_id INT NOT NULL, redmine_ticket_id INT NOT NULL, INDEX IDX_9C1DEA892062A873 (spent_time_id), INDEX IDX_9C1DEA897E8AFB09 (redmine_ticket_id), PRIMARY KEY(spent_time_id, redmine_ticket_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_entries (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT DEFAULT NULL, custom_action VARCHAR(255) DEFAULT NULL, extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE redmine_ticket (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, lastSyncAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE redmine_spent_time (id INT AUTO_INCREMENT NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', lastSyncAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__sector_of_activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__address (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(16) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, street_number VARCHAR(255) DEFAULT NULL, postbox VARCHAR(32) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(16) DEFAULT NULL, INDEX IDX_1781C35719EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__project (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, fileset_id INT DEFAULT NULL, object_identity_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_357C7C3819EB6921 (client_id), INDEX IDX_357C7C38304051B3 (fileset_id), INDEX IDX_357C7C383D9AB4A6 (object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_group (project_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_7E954D5B166D1F9C (project_id), INDEX IDX_7E954D5BFE54D947 (group_id), PRIMARY KEY(project_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__client (id INT AUTO_INCREMENT NOT NULL, fileset_id INT DEFAULT NULL, object_identity_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, project_manager_id INT DEFAULT NULL, financial_information VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, referral VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, company_name VARCHAR(255) DEFAULT NULL, company_sector_of_activity VARCHAR(255) DEFAULT NULL, company_country VARCHAR(8) DEFAULT NULL, company_website VARCHAR(255) DEFAULT NULL, company_email VARCHAR(255) DEFAULT NULL, company_phone1 VARCHAR(255) DEFAULT NULL, company_phone2 VARCHAR(255) DEFAULT NULL, company_fax VARCHAR(255) DEFAULT NULL, status VARCHAR(16) DEFAULT NULL, INDEX IDX_D23CAF41304051B3 (fileset_id), INDEX IDX_D23CAF413D9AB4A6 (object_identity_id), INDEX IDX_D23CAF417E3C61F9 (owner_id), INDEX IDX_D23CAF4160984F51 (project_manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_client_group (abstract_client_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_5DC66143CC7B85E1 (abstract_client_id), INDEX IDX_5DC66143FE54D947 (group_id), PRIMARY KEY(abstract_client_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__contact (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, note LONGTEXT DEFAULT NULL, title LONGTEXT DEFAULT NULL, person_first_name VARCHAR(255) DEFAULT NULL, person_last_name VARCHAR(255) DEFAULT NULL, person_date_of_birth DATETIME DEFAULT NULL, person_gender VARCHAR(2) DEFAULT NULL, person_nationality VARCHAR(2) DEFAULT NULL, person_direct_line_phone VARCHAR(64) DEFAULT NULL, person_company_phone VARCHAR(64) DEFAULT NULL, person_private_phone VARCHAR(64) DEFAULT NULL, person_company_email VARCHAR(128) DEFAULT NULL, person_private_email VARCHAR(128) DEFAULT NULL, person_skype_id VARCHAR(64) DEFAULT NULL, person_facebook_id VARCHAR(64) DEFAULT NULL, person_twitter VARCHAR(64) DEFAULT NULL, person_instagram VARCHAR(64) DEFAULT NULL, INDEX IDX_56AD4AEE19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_group (contact_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_40EA54CAE7A1254A (contact_id), INDEX IDX_40EA54CAFE54D947 (group_id), PRIMARY KEY(contact_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__project_membership (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, user_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_40D2806A166D1F9C (project_id), INDEX IDX_40D2806AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crm__custom_property (id INT AUTO_INCREMENT NOT NULL, object_identity_id INT DEFAULT NULL, client_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_E4B71E793D9AB4A6 (object_identity_id), INDEX IDX_E4B71E7919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user__group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_82AAB3645E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user__user (id INT AUTO_INCREMENT NOT NULL, object_identity_id INT DEFAULT NULL, primary_group_id INT DEFAULT NULL, fileset_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, biography VARCHAR(1000) DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, timezone VARCHAR(64) DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, facebook_uid VARCHAR(255) DEFAULT NULL, facebook_name VARCHAR(255) DEFAULT NULL, facebook_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', twitter_uid VARCHAR(255) DEFAULT NULL, twitter_name VARCHAR(255) DEFAULT NULL, twitter_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', gplus_uid VARCHAR(255) DEFAULT NULL, gplus_name VARCHAR(255) DEFAULT NULL, gplus_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', token VARCHAR(255) DEFAULT NULL, two_step_code VARCHAR(255) DEFAULT NULL, redmine_user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, work_permit INT DEFAULT NULL, private_email VARCHAR(255) DEFAULT NULL, work_line VARCHAR(255) DEFAULT NULL, work_mobile_line VARCHAR(255) DEFAULT NULL, private_home_line VARCHAR(255) DEFAULT NULL, private_mobile_line VARCHAR(255) DEFAULT NULL, private_address VARCHAR(255) DEFAULT NULL, holidays_remaining INT DEFAULT NULL, redmine_auth_token VARCHAR(128) DEFAULT NULL, UNIQUE INDEX UNIQ_32745D0A92FC23A8 (username_canonical), UNIQUE INDEX UNIQ_32745D0AA0D96FBF (email_canonical), UNIQUE INDEX UNIQ_32745D0A3D9AB4A6 (object_identity_id), INDEX IDX_32745D0ADF2BB4B6 (primary_group_id), INDEX IDX_32745D0A304051B3 (fileset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user__user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_45528670A76ED395 (user_id), INDEX IDX_45528670FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE geolocation (id INT AUTO_INCREMENT NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, postal_code VARCHAR(16) DEFAULT NULL, country VARCHAR(3) DEFAULT NULL, city VARCHAR(128) DEFAULT NULL, street_number VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, radius INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system__config_key (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saga__state (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(255) NOT NULL, `values` LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', done TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX hash_ix (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__task (id INT AUTO_INCREMENT NOT NULL, author_object_identity_id INT DEFAULT NULL, assignee_object_identity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status INT NOT NULL, extra_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2B1BC5398F29EE9 (author_object_identity_id), INDEX IDX_2B1BC53C148799A (assignee_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__task_notes (task_id INT NOT NULL, note_id INT NOT NULL, INDEX IDX_724428CD8DB60186 (task_id), INDEX IDX_724428CD26ED0855 (note_id), PRIMARY KEY(task_id, note_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__task__related_oid (task_id INT NOT NULL, object_identity_interface_id INT NOT NULL, INDEX IDX_50AF4968DB60186 (task_id), INDEX IDX_50AF496A0987314 (object_identity_interface_id), PRIMARY KEY(task_id, object_identity_interface_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__note (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, author_object_identity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type INT DEFAULT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9F729D628DB60186 (task_id), INDEX IDX_9F729D6298F29EE9 (author_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__note__related_oid (note_id INT NOT NULL, object_identity_interface_id INT NOT NULL, INDEX IDX_506F1E8326ED0855 (note_id), INDEX IDX_506F1E83A0987314 (object_identity_interface_id), PRIMARY KEY(note_id, object_identity_interface_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticketing__reminder (id INT AUTO_INCREMENT NOT NULL, note_id INT DEFAULT NULL, assignee_object_identity_id INT DEFAULT NULL, notification_time DATETIME NOT NULL, status INT NOT NULL, is_expired_notification_sent TINYINT(1) NOT NULL, INDEX IDX_64F2F7D026ED0855 (note_id), INDEX IDX_64F2F7D0C148799A (assignee_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE object_identity__index (property INT NOT NULL, language VARCHAR(4) NOT NULL, objectidentity_id INT NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_A01544A9EFEA8D6C (objectidentity_id), FULLTEXT INDEX search_index (content), INDEX language (language), PRIMARY KEY(property, language, objectidentity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM');
        $this->addSql('CREATE TABLE object_identity (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, abstract_client_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, canonical_name VARCHAR(255) DEFAULT NULL, type VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_BC4304AC166D1F9C (project_id), UNIQUE INDEX UNIQ_BC4304ACCC7B85E1 (abstract_client_id), UNIQUE INDEX UNIQ_BC4304AC2ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__media (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_status INT NOT NULL, provider_reference VARCHAR(255) NOT NULL, provider_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', width INT DEFAULT NULL, height INT DEFAULT NULL, length NUMERIC(10, 0) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_size INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, author_name VARCHAR(255) DEFAULT NULL, context VARCHAR(64) DEFAULT NULL, cdn_is_flushable TINYINT(1) DEFAULT NULL, cdn_flush_identifier VARCHAR(64) DEFAULT NULL, cdn_flush_at DATETIME DEFAULT NULL, cdn_status INT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5C6DD74E12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, context VARCHAR(64) NOT NULL, default_format VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media__gallery_media (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, media_id INT DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_80D4C5414E7AF8F (gallery_id), INDEX IDX_80D4C541EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classification__context (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classification__category (id INT AUTO_INCREMENT NOT NULL, context_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_43629B366B00C1CF (context_id), INDEX IDX_43629B36727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounting__invoice ADD CONSTRAINT FK_5CA9DBCB166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id)');
        $this->addSql('ALTER TABLE accounting__invoice ADD CONSTRAINT FK_5CA9DBCB134AABF4 FOREIGN KEY (client_billing_address_id) REFERENCES crm__address (id)');
        $this->addSql('ALTER TABLE accounting__invoice ADD CONSTRAINT FK_5CA9DBCBA8EC2BA7 FOREIGN KEY (file_set_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE accounting__invoice ADD CONSTRAINT FK_5CA9DBCBB03A8386 FOREIGN KEY (created_by_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE invoice_work ADD CONSTRAINT FK_518B0AE52989F1FD FOREIGN KEY (invoice_id) REFERENCES accounting__invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice_work ADD CONSTRAINT FK_518B0AE5BB3453DB FOREIGN KEY (work_id) REFERENCES accounting__work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__invoice_payment ADD CONSTRAINT FK_54980B422989F1FD FOREIGN KEY (invoice_id) REFERENCES accounting__invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__invoice_payment ADD CONSTRAINT FK_54980B424C3A3BB FOREIGN KEY (payment_id) REFERENCES accounting__payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__work ADD CONSTRAINT FK_FE2611C1166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id)');
        $this->addSql('ALTER TABLE accounting__work ADD CONSTRAINT FK_FE2611C1A8EC2BA7 FOREIGN KEY (file_set_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE accounting__work ADD CONSTRAINT FK_FE2611C1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE work_invoice ADD CONSTRAINT FK_45AEAF66BB3453DB FOREIGN KEY (work_id) REFERENCES accounting__work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_invoice ADD CONSTRAINT FK_45AEAF662989F1FD FOREIGN KEY (invoice_id) REFERENCES accounting__invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__work_redmine_ticket ADD CONSTRAINT FK_FB1DCBEBB3453DB FOREIGN KEY (work_id) REFERENCES accounting__work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__work_redmine_ticket ADD CONSTRAINT FK_FB1DCBE7E8AFB09 FOREIGN KEY (redmine_ticket_id) REFERENCES redmine_ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__payment ADD CONSTRAINT FK_A1E4488219EB6921 FOREIGN KEY (client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE accounting__work_schedule ADD CONSTRAINT FK_E8A006EABB3453DB FOREIGN KEY (work_id) REFERENCES accounting__work (id)');
        $this->addSql('ALTER TABLE accounting__work_schedule ADD CONSTRAINT FK_E8A006EAA76ED395 FOREIGN KEY (user_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE accounting__work_schedule ADD CONSTRAINT FK_E8A006EAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE accounting__work_schedule_redmine_ticket ADD CONSTRAINT FK_B95560A4BBCA2216 FOREIGN KEY (work_schedule_id) REFERENCES accounting__work_schedule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__work_schedule_redmine_ticket ADD CONSTRAINT FK_B95560A47E8AFB09 FOREIGN KEY (redmine_ticket_id) REFERENCES redmine_ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__spent_time ADD CONSTRAINT FK_36011663166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id)');
        $this->addSql('ALTER TABLE accounting__spent_time ADD CONSTRAINT FK_36011663BB3453DB FOREIGN KEY (work_id) REFERENCES accounting__work (id)');
        $this->addSql('ALTER TABLE accounting__spent_time ADD CONSTRAINT FK_36011663A76ED395 FOREIGN KEY (user_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE accounting__spent_time ADD CONSTRAINT FK_360116635766EA4 FOREIGN KEY (redmine_time_entry_id) REFERENCES redmine_spent_time (id)');
        $this->addSql('ALTER TABLE accounting__spent_time_redmine_ticket ADD CONSTRAINT FK_9C1DEA892062A873 FOREIGN KEY (spent_time_id) REFERENCES accounting__spent_time (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accounting__spent_time_redmine_ticket ADD CONSTRAINT FK_9C1DEA897E8AFB09 FOREIGN KEY (redmine_ticket_id) REFERENCES redmine_ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crm__address ADD CONSTRAINT FK_1781C35719EB6921 FOREIGN KEY (client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE crm__project ADD CONSTRAINT FK_357C7C3819EB6921 FOREIGN KEY (client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE crm__project ADD CONSTRAINT FK_357C7C38304051B3 FOREIGN KEY (fileset_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE crm__project ADD CONSTRAINT FK_357C7C383D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE project_group ADD CONSTRAINT FK_7E954D5B166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_group ADD CONSTRAINT FK_7E954D5BFE54D947 FOREIGN KEY (group_id) REFERENCES user__group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crm__client ADD CONSTRAINT FK_D23CAF41304051B3 FOREIGN KEY (fileset_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE crm__client ADD CONSTRAINT FK_D23CAF413D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE crm__client ADD CONSTRAINT FK_D23CAF417E3C61F9 FOREIGN KEY (owner_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE crm__client ADD CONSTRAINT FK_D23CAF4160984F51 FOREIGN KEY (project_manager_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE abstract_client_group ADD CONSTRAINT FK_5DC66143CC7B85E1 FOREIGN KEY (abstract_client_id) REFERENCES crm__client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_client_group ADD CONSTRAINT FK_5DC66143FE54D947 FOREIGN KEY (group_id) REFERENCES user__group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crm__contact ADD CONSTRAINT FK_56AD4AEE19EB6921 FOREIGN KEY (client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE contact_group ADD CONSTRAINT FK_40EA54CAE7A1254A FOREIGN KEY (contact_id) REFERENCES crm__contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_group ADD CONSTRAINT FK_40EA54CAFE54D947 FOREIGN KEY (group_id) REFERENCES user__group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crm__project_membership ADD CONSTRAINT FK_40D2806A166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id)');
        $this->addSql('ALTER TABLE crm__project_membership ADD CONSTRAINT FK_40D2806AA76ED395 FOREIGN KEY (user_id) REFERENCES user__user (id)');
        $this->addSql('ALTER TABLE crm__custom_property ADD CONSTRAINT FK_E4B71E793D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE crm__custom_property ADD CONSTRAINT FK_E4B71E7919EB6921 FOREIGN KEY (client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE user__user ADD CONSTRAINT FK_32745D0A3D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE user__user ADD CONSTRAINT FK_32745D0ADF2BB4B6 FOREIGN KEY (primary_group_id) REFERENCES user__group (id)');
        $this->addSql('ALTER TABLE user__user ADD CONSTRAINT FK_32745D0A304051B3 FOREIGN KEY (fileset_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE user__user_group ADD CONSTRAINT FK_45528670A76ED395 FOREIGN KEY (user_id) REFERENCES user__user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user__user_group ADD CONSTRAINT FK_45528670FE54D947 FOREIGN KEY (group_id) REFERENCES user__group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticketing__task ADD CONSTRAINT FK_2B1BC5398F29EE9 FOREIGN KEY (author_object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE ticketing__task ADD CONSTRAINT FK_2B1BC53C148799A FOREIGN KEY (assignee_object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE ticketing__task_notes ADD CONSTRAINT FK_724428CD8DB60186 FOREIGN KEY (task_id) REFERENCES ticketing__task (id)');
        $this->addSql('ALTER TABLE ticketing__task_notes ADD CONSTRAINT FK_724428CD26ED0855 FOREIGN KEY (note_id) REFERENCES ticketing__note (id)');
        $this->addSql('ALTER TABLE ticketing__task__related_oid ADD CONSTRAINT FK_50AF4968DB60186 FOREIGN KEY (task_id) REFERENCES ticketing__task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticketing__task__related_oid ADD CONSTRAINT FK_50AF496A0987314 FOREIGN KEY (object_identity_interface_id) REFERENCES object_identity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticketing__note ADD CONSTRAINT FK_9F729D628DB60186 FOREIGN KEY (task_id) REFERENCES ticketing__task (id)');
        $this->addSql('ALTER TABLE ticketing__note ADD CONSTRAINT FK_9F729D6298F29EE9 FOREIGN KEY (author_object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE ticketing__note__related_oid ADD CONSTRAINT FK_506F1E8326ED0855 FOREIGN KEY (note_id) REFERENCES ticketing__note (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticketing__note__related_oid ADD CONSTRAINT FK_506F1E83A0987314 FOREIGN KEY (object_identity_interface_id) REFERENCES object_identity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticketing__reminder ADD CONSTRAINT FK_64F2F7D026ED0855 FOREIGN KEY (note_id) REFERENCES ticketing__note (id)');
        $this->addSql('ALTER TABLE ticketing__reminder ADD CONSTRAINT FK_64F2F7D0C148799A FOREIGN KEY (assignee_object_identity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE object_identity__index ADD CONSTRAINT FK_A01544A9EFEA8D6C FOREIGN KEY (objectidentity_id) REFERENCES object_identity (id)');
        $this->addSql('ALTER TABLE object_identity ADD CONSTRAINT FK_BC4304AC166D1F9C FOREIGN KEY (project_id) REFERENCES crm__project (id)');
        $this->addSql('ALTER TABLE object_identity ADD CONSTRAINT FK_BC4304ACCC7B85E1 FOREIGN KEY (abstract_client_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE object_identity ADD CONSTRAINT FK_BC4304AC2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES crm__client (id)');
        $this->addSql('ALTER TABLE media__media ADD CONSTRAINT FK_5C6DD74E12469DE2 FOREIGN KEY (category_id) REFERENCES classification__category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE media__gallery_media ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE classification__category ADD CONSTRAINT FK_43629B366B00C1CF FOREIGN KEY (context_id) REFERENCES classification__context (id)');
        $this->addSql('ALTER TABLE classification__category ADD CONSTRAINT FK_43629B36727ACA70 FOREIGN KEY (parent_id) REFERENCES classification__category (id)');
        $this->addSql('ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('INSERT INTO `user__user` (`id`, `object_identity_id`, `primary_group_id`, `fileset_id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `created_at`, `updated_at`, `date_of_birth`, `firstname`, `lastname`, `website`, `biography`, `gender`, `locale`, `timezone`, `phone`, `facebook_uid`, `facebook_name`, `facebook_data`, `twitter_uid`, `twitter_name`, `twitter_data`, `gplus_uid`, `gplus_name`, `gplus_data`, `token`, `two_step_code`, `redmine_user_id`, `title`, `nationality`, `work_permit`, `private_email`, `work_line`, `work_mobile_line`, `private_home_line`, `private_mobile_line`, `private_address`, `holidays_remaining`, `redmine_auth_token`)
VALUES
	(1, 1, NULL, 1, \'admin\', \'admin\', \'admin@crm\', \'admin@crm\', 1, \'spyn55cvz28w8ccccog8c0kcogosks4\', \'fe0cf232c487cf38b82daa0408ff8f3eb17f20654cd2bd837ffd95f685ceca000a60ce0516b14dd2329046fd812a6e0e0be05b700fdf938bd67bd6c8a8ff34ca\', NULL, 0, 0, NULL, NULL, NULL, \'a:4:{i:0;s:17:\"ROLE_SONATA_ADMIN\";i:1;s:10:\"ROLE_SALES\";i:2;s:10:\"ROLE_ADMIN\";i:3;s:16:\"ROLE_SUPER_ADMIN\";}\', 0, NULL, \'2017-08-27 21:40:56\', \'2017-08-27 21:40:56\', NULL, NULL, NULL, NULL, NULL, \'u\', NULL, NULL, NULL, NULL, NULL, \'null\', NULL, NULL, \'null\', NULL, NULL, \'null\', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice_work DROP FOREIGN KEY FK_518B0AE52989F1FD');
        $this->addSql('ALTER TABLE accounting__invoice_payment DROP FOREIGN KEY FK_54980B422989F1FD');
        $this->addSql('ALTER TABLE work_invoice DROP FOREIGN KEY FK_45AEAF662989F1FD');
        $this->addSql('ALTER TABLE invoice_work DROP FOREIGN KEY FK_518B0AE5BB3453DB');
        $this->addSql('ALTER TABLE work_invoice DROP FOREIGN KEY FK_45AEAF66BB3453DB');
        $this->addSql('ALTER TABLE accounting__work_redmine_ticket DROP FOREIGN KEY FK_FB1DCBEBB3453DB');
        $this->addSql('ALTER TABLE accounting__work_schedule DROP FOREIGN KEY FK_E8A006EABB3453DB');
        $this->addSql('ALTER TABLE accounting__spent_time DROP FOREIGN KEY FK_36011663BB3453DB');
        $this->addSql('ALTER TABLE accounting__invoice_payment DROP FOREIGN KEY FK_54980B424C3A3BB');
        $this->addSql('ALTER TABLE accounting__work_schedule_redmine_ticket DROP FOREIGN KEY FK_B95560A4BBCA2216');
        $this->addSql('ALTER TABLE accounting__spent_time_redmine_ticket DROP FOREIGN KEY FK_9C1DEA892062A873');
        $this->addSql('ALTER TABLE accounting__work_redmine_ticket DROP FOREIGN KEY FK_FB1DCBE7E8AFB09');
        $this->addSql('ALTER TABLE accounting__work_schedule_redmine_ticket DROP FOREIGN KEY FK_B95560A47E8AFB09');
        $this->addSql('ALTER TABLE accounting__spent_time_redmine_ticket DROP FOREIGN KEY FK_9C1DEA897E8AFB09');
        $this->addSql('ALTER TABLE accounting__spent_time DROP FOREIGN KEY FK_360116635766EA4');
        $this->addSql('ALTER TABLE accounting__invoice DROP FOREIGN KEY FK_5CA9DBCB134AABF4');
        $this->addSql('ALTER TABLE accounting__invoice DROP FOREIGN KEY FK_5CA9DBCB166D1F9C');
        $this->addSql('ALTER TABLE accounting__work DROP FOREIGN KEY FK_FE2611C1166D1F9C');
        $this->addSql('ALTER TABLE accounting__spent_time DROP FOREIGN KEY FK_36011663166D1F9C');
        $this->addSql('ALTER TABLE project_group DROP FOREIGN KEY FK_7E954D5B166D1F9C');
        $this->addSql('ALTER TABLE crm__project_membership DROP FOREIGN KEY FK_40D2806A166D1F9C');
        $this->addSql('ALTER TABLE object_identity DROP FOREIGN KEY FK_BC4304AC166D1F9C');
        $this->addSql('ALTER TABLE accounting__payment DROP FOREIGN KEY FK_A1E4488219EB6921');
        $this->addSql('ALTER TABLE crm__address DROP FOREIGN KEY FK_1781C35719EB6921');
        $this->addSql('ALTER TABLE crm__project DROP FOREIGN KEY FK_357C7C3819EB6921');
        $this->addSql('ALTER TABLE abstract_client_group DROP FOREIGN KEY FK_5DC66143CC7B85E1');
        $this->addSql('ALTER TABLE crm__contact DROP FOREIGN KEY FK_56AD4AEE19EB6921');
        $this->addSql('ALTER TABLE crm__custom_property DROP FOREIGN KEY FK_E4B71E7919EB6921');
        $this->addSql('ALTER TABLE object_identity DROP FOREIGN KEY FK_BC4304ACCC7B85E1');
        $this->addSql('ALTER TABLE object_identity DROP FOREIGN KEY FK_BC4304AC2ADD6D8C');
        $this->addSql('ALTER TABLE contact_group DROP FOREIGN KEY FK_40EA54CAE7A1254A');
        $this->addSql('ALTER TABLE project_group DROP FOREIGN KEY FK_7E954D5BFE54D947');
        $this->addSql('ALTER TABLE abstract_client_group DROP FOREIGN KEY FK_5DC66143FE54D947');
        $this->addSql('ALTER TABLE contact_group DROP FOREIGN KEY FK_40EA54CAFE54D947');
        $this->addSql('ALTER TABLE user__user DROP FOREIGN KEY FK_32745D0ADF2BB4B6');
        $this->addSql('ALTER TABLE user__user_group DROP FOREIGN KEY FK_45528670FE54D947');
        $this->addSql('ALTER TABLE accounting__invoice DROP FOREIGN KEY FK_5CA9DBCBB03A8386');
        $this->addSql('ALTER TABLE accounting__work DROP FOREIGN KEY FK_FE2611C1B03A8386');
        $this->addSql('ALTER TABLE accounting__work_schedule DROP FOREIGN KEY FK_E8A006EAA76ED395');
        $this->addSql('ALTER TABLE accounting__work_schedule DROP FOREIGN KEY FK_E8A006EAB03A8386');
        $this->addSql('ALTER TABLE accounting__spent_time DROP FOREIGN KEY FK_36011663A76ED395');
        $this->addSql('ALTER TABLE crm__client DROP FOREIGN KEY FK_D23CAF417E3C61F9');
        $this->addSql('ALTER TABLE crm__client DROP FOREIGN KEY FK_D23CAF4160984F51');
        $this->addSql('ALTER TABLE crm__project_membership DROP FOREIGN KEY FK_40D2806AA76ED395');
        $this->addSql('ALTER TABLE user__user_group DROP FOREIGN KEY FK_45528670A76ED395');
        $this->addSql('ALTER TABLE ticketing__task_notes DROP FOREIGN KEY FK_724428CD8DB60186');
        $this->addSql('ALTER TABLE ticketing__task__related_oid DROP FOREIGN KEY FK_50AF4968DB60186');
        $this->addSql('ALTER TABLE ticketing__note DROP FOREIGN KEY FK_9F729D628DB60186');
        $this->addSql('ALTER TABLE ticketing__task_notes DROP FOREIGN KEY FK_724428CD26ED0855');
        $this->addSql('ALTER TABLE ticketing__note__related_oid DROP FOREIGN KEY FK_506F1E8326ED0855');
        $this->addSql('ALTER TABLE ticketing__reminder DROP FOREIGN KEY FK_64F2F7D026ED0855');
        $this->addSql('ALTER TABLE crm__project DROP FOREIGN KEY FK_357C7C383D9AB4A6');
        $this->addSql('ALTER TABLE crm__client DROP FOREIGN KEY FK_D23CAF413D9AB4A6');
        $this->addSql('ALTER TABLE crm__custom_property DROP FOREIGN KEY FK_E4B71E793D9AB4A6');
        $this->addSql('ALTER TABLE user__user DROP FOREIGN KEY FK_32745D0A3D9AB4A6');
        $this->addSql('ALTER TABLE ticketing__task DROP FOREIGN KEY FK_2B1BC5398F29EE9');
        $this->addSql('ALTER TABLE ticketing__task DROP FOREIGN KEY FK_2B1BC53C148799A');
        $this->addSql('ALTER TABLE ticketing__task__related_oid DROP FOREIGN KEY FK_50AF496A0987314');
        $this->addSql('ALTER TABLE ticketing__note DROP FOREIGN KEY FK_9F729D6298F29EE9');
        $this->addSql('ALTER TABLE ticketing__note__related_oid DROP FOREIGN KEY FK_506F1E83A0987314');
        $this->addSql('ALTER TABLE ticketing__reminder DROP FOREIGN KEY FK_64F2F7D0C148799A');
        $this->addSql('ALTER TABLE object_identity__index DROP FOREIGN KEY FK_A01544A9EFEA8D6C');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C541EA9FDD75');
        $this->addSql('ALTER TABLE accounting__invoice DROP FOREIGN KEY FK_5CA9DBCBA8EC2BA7');
        $this->addSql('ALTER TABLE accounting__work DROP FOREIGN KEY FK_FE2611C1A8EC2BA7');
        $this->addSql('ALTER TABLE crm__project DROP FOREIGN KEY FK_357C7C38304051B3');
        $this->addSql('ALTER TABLE crm__client DROP FOREIGN KEY FK_D23CAF41304051B3');
        $this->addSql('ALTER TABLE user__user DROP FOREIGN KEY FK_32745D0A304051B3');
        $this->addSql('ALTER TABLE media__gallery_media DROP FOREIGN KEY FK_80D4C5414E7AF8F');
        $this->addSql('ALTER TABLE classification__category DROP FOREIGN KEY FK_43629B366B00C1CF');
        $this->addSql('ALTER TABLE media__media DROP FOREIGN KEY FK_5C6DD74E12469DE2');
        $this->addSql('ALTER TABLE classification__category DROP FOREIGN KEY FK_43629B36727ACA70');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9');
        $this->addSql('ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE accounting__invoice');
        $this->addSql('DROP TABLE invoice_work');
        $this->addSql('DROP TABLE accounting__invoice_payment');
        $this->addSql('DROP TABLE accounting__work');
        $this->addSql('DROP TABLE work_invoice');
        $this->addSql('DROP TABLE accounting__work_redmine_ticket');
        $this->addSql('DROP TABLE accounting__payment');
        $this->addSql('DROP TABLE accounting__work_schedule');
        $this->addSql('DROP TABLE accounting__work_schedule_redmine_ticket');
        $this->addSql('DROP TABLE accounting__spent_time');
        $this->addSql('DROP TABLE accounting__spent_time_redmine_ticket');
        $this->addSql('DROP TABLE log_entries');
        $this->addSql('DROP TABLE redmine_ticket');
        $this->addSql('DROP TABLE redmine_spent_time');
        $this->addSql('DROP TABLE crm__sector_of_activity');
        $this->addSql('DROP TABLE crm__address');
        $this->addSql('DROP TABLE crm__project');
        $this->addSql('DROP TABLE project_group');
        $this->addSql('DROP TABLE crm__client');
        $this->addSql('DROP TABLE abstract_client_group');
        $this->addSql('DROP TABLE crm__contact');
        $this->addSql('DROP TABLE contact_group');
        $this->addSql('DROP TABLE crm__project_membership');
        $this->addSql('DROP TABLE crm__custom_property');
        $this->addSql('DROP TABLE user__group');
        $this->addSql('DROP TABLE user__user');
        $this->addSql('DROP TABLE user__user_group');
        $this->addSql('DROP TABLE geolocation');
        $this->addSql('DROP TABLE system__config_key');
        $this->addSql('DROP TABLE saga__state');
        $this->addSql('DROP TABLE ticketing__task');
        $this->addSql('DROP TABLE ticketing__task_notes');
        $this->addSql('DROP TABLE ticketing__task__related_oid');
        $this->addSql('DROP TABLE ticketing__note');
        $this->addSql('DROP TABLE ticketing__note__related_oid');
        $this->addSql('DROP TABLE ticketing__reminder');
        $this->addSql('DROP TABLE object_identity__index');
        $this->addSql('DROP TABLE object_identity');
        $this->addSql('DROP TABLE media__media');
        $this->addSql('DROP TABLE media__gallery');
        $this->addSql('DROP TABLE media__gallery_media');
        $this->addSql('DROP TABLE classification__context');
        $this->addSql('DROP TABLE classification__category');
        $this->addSql('DROP TABLE acl_classes');
        $this->addSql('DROP TABLE acl_security_identities');
        $this->addSql('DROP TABLE acl_object_identities');
        $this->addSql('DROP TABLE acl_object_identity_ancestors');
        $this->addSql('DROP TABLE acl_entries');
    }
}