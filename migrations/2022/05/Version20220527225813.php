<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527225813 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE alias (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, maiden TINYINT(1) DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sortable_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, married TINYINT(1) DEFAULT NULL, FULLTEXT INDEX IDX_E16C6B945E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE book (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE collection (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE contribution (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, person_id INT NOT NULL, publication_id INT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EA351E1538B217A7 (publication_id), INDEX IDX_EA351E15D60322AC (role_id), INDEX IDX_EA351E15217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE date_year (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start INT DEFAULT NULL, start_circa TINYINT(1) DEFAULT 0 NOT NULL, end INT DEFAULT NULL, end_circa TINYINT(1) DEFAULT 0 NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B5CFAC39F79558F (start), INDEX IDX_9B5CFAC3FC33B1 (end), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_835033F86DE44026 (description), FULLTEXT INDEX IDX_835033F8EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_835033F85E237E06 (name), FULLTEXT INDEX IDX_835033F8EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_blog_page (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, weight INT NOT NULL, public TINYINT(1) NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, excerpt LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, searchable LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', include_comments TINYINT(1) NOT NULL, homepage TINYINT(1) DEFAULT 0 NOT NULL, in_menu TINYINT(1) NOT NULL, INDEX IDX_23FD24C7A76ED395 (user_id), FULLTEXT INDEX blog_page_ft (title, searchable), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_blog_post (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, status_id INT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, excerpt LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, searchable LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', include_comments TINYINT(1) NOT NULL, INDEX IDX_6D7DFE6AA76ED395 (user_id), FULLTEXT INDEX blog_post_ft (title, searchable), INDEX IDX_6D7DFE6A12469DE2 (category_id), INDEX IDX_6D7DFE6A6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_blog_post_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_32F5FC8C6DE44026 (description), FULLTEXT INDEX IDX_32F5FC8CEA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_32F5FC8C5E237E06 (name), FULLTEXT INDEX IDX_32F5FC8CEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_blog_post_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, public TINYINT(1) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_4A63E2FD6DE44026 (description), FULLTEXT INDEX IDX_4A63E2FDEA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_4A63E2FD5E237E06 (name), FULLTEXT INDEX IDX_4A63E2FDEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_feedback_comment (id INT AUTO_INCREMENT NOT NULL, status_id INT NOT NULL, fullname VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, follow_up TINYINT(1) NOT NULL, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DD5C8DB56BF700BD (status_id), FULLTEXT INDEX comment_ft (fullname, content), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_feedback_comment_note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, comment_id INT NOT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX comment_note_ft (content), INDEX IDX_4BC0F0BA76ED395 (user_id), INDEX IDX_4BC0F0BF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_feedback_comment_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_7B8DA6106DE44026 (description), FULLTEXT INDEX IDX_7B8DA610EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_7B8DA6105E237E06 (name), FULLTEXT INDEX IDX_7B8DA610EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_audio (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, INDEX IDX_9D15F751E284468 (entity), FULLTEXT INDEX nines_media_audio_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_image (id INT AUTO_INCREMENT NOT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, thumb_path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, image_width INT NOT NULL, image_height INT NOT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4055C59BE284468 (entity), FULLTEXT INDEX nines_media_image_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_link (id INT AUTO_INCREMENT NOT NULL, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, text VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3B5D85A3E284468 (entity), FULLTEXT INDEX nines_media_link_ft (url, text), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_pdf (id INT AUTO_INCREMENT NOT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, thumb_path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9286B706E284468 (entity), FULLTEXT INDEX nines_media_pdf_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, reset_expiry DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', fullname VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, affiliation VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5BA994A1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE periodical (id INT NOT NULL, run_dates VARCHAR(48) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, continued_from LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, continued_by LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, birth_date_id INT DEFAULT NULL, birth_place_id INT DEFAULT NULL, death_date_id INT DEFAULT NULL, death_place_id INT DEFAULT NULL, full_name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sortable_name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', gender VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, url_links LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', canadian TINYINT(1) DEFAULT 1, INDEX IDX_34DCD176B4BB6BBC (birth_place_id), INDEX IDX_34DCD176AD45A6FD (death_place_id), UNIQUE INDEX UNIQ_34DCD176C38A9A1D (birth_date_id), INDEX IDX_34DCD1763BD1838 (sortable_name), UNIQUE INDEX UNIQ_34DCD1765171AA8B (death_date_id), FULLTEXT INDEX IDX_34DCD176DBC463C4 (full_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE person_alias (person_id INT NOT NULL, alias_id INT NOT NULL, INDEX IDX_4D5A74505E564AE2 (alias_id), INDEX IDX_4D5A7450217BBB47 (person_id), PRIMARY KEY(person_id, alias_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE person_place (person_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_D82B4C09DA6A219 (place_id), INDEX IDX_D82B4C09217BBB47 (person_id), PRIMARY KEY(person_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, country_name VARCHAR(250) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, latitude NUMERIC(9, 6) DEFAULT NULL, longitude NUMERIC(9, 6) DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sortable_name VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, region_name VARCHAR(250) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, geonames_id VARCHAR(16) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, FULLTEXT INDEX IDX_741D53CD3BD1838 (sortable_name), FULLTEXT INDEX IDX_741D53CD5E237E06D910F5E2 (name, country_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, date_year_id INT DEFAULT NULL, location_id INT DEFAULT NULL, title LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sortable_title LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, links LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, FULLTEXT INDEX IDX_AF3C67792B36786B (title), FULLTEXT INDEX IDX_AF3C6779EA0AFBA6 (sortable_title), UNIQUE INDEX UNIQ_AF3C67797926F95A (date_year_id), INDEX IDX_AF3C677964D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE publication_publisher (publication_id INT NOT NULL, publisher_id INT NOT NULL, INDEX IDX_80ABD3D140C86FCE (publisher_id), INDEX IDX_80ABD3D138B217A7 (publication_id), PRIMARY KEY(publication_id, publisher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE publications_genres (publication_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_F788A3B04296D31F (genre_id), INDEX IDX_F788A3B038B217A7 (publication_id), PRIMARY KEY(publication_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE publisher (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL, updated DATETIME NOT NULL, notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, FULLTEXT INDEX IDX_9CE8D5465E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE publisher_place (publisher_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_81F38E4ADA6A219 (place_id), INDEX IDX_81F38E4A40C86FCE (publisher_id), PRIMARY KEY(publisher_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL, updated DATETIME NOT NULL, FULLTEXT INDEX IDX_57698A6A6DE44026 (description), FULLTEXT INDEX IDX_57698A6AEA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_57698A6A5E237E06 (name), FULLTEXT INDEX IDX_57698A6AEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE alias');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE book');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE collection');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE contribution');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE date_year');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE genre');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_blog_page');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_blog_post');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_blog_post_category');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_blog_post_status');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_feedback_comment');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_feedback_comment_note');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_feedback_comment_status');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_audio');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_image');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_link');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_pdf');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_user');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE periodical');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE person');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE person_alias');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE person_place');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE place');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE publication');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE publication_publisher');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE publications_genres');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE publisher');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE publisher_place');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE role');
    }
}
