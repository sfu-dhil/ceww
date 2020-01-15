<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114203116 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_5BA994A192FC23A8 ON nines_user');
        $this->addSql('DROP INDEX UNIQ_5BA994A1A0D96FBF ON nines_user');
        $this->addSql('DROP INDEX UNIQ_5BA994A1C05FB297 ON nines_user');

        $this->addSql(
            <<<'ENDSQL'
ALTER TABLE nines_user
    DROP username_canonical, 
    DROP email_canonical, 
    DROP email,
    DROP salt,
    DROP data,

	CHANGE fullname fullname varchar(64) NOT NULL,
	CHANGE enabled active TINYINT NOT NULL DEFAULT 0,
 	RENAME COLUMN username TO email,
    RENAME COLUMN confirmation_token TO reset_token,
    RENAME COLUMN password_requested_at TO reset_expiry,
    RENAME COLUMN institution TO affiliation,
    RENAME COLUMN last_login TO login,
    
    ADD created DATETIME NOT NULL DEFAULT NOW() COMMENT '(DC2Type:datetime_immutable)', 
    ADD updated DATETIME NOT NULL DEFAULT NOW() COMMENT '(DC2Type:datetime_immutable)'    
;
ENDSQL
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_5BA994A1E7927C74 ON nines_user (email)');
    }

    public function down(Schema $schema) : void {
    }
}
