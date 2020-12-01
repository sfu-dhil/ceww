<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201230335 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flag_entity DROP FOREIGN KEY FK_E37C49B9919FE4E5');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE flag');
        $this->addSql('DROP TABLE flag_entity');
        $this->addSql('ALTER TABLE nines_user CHANGE active active TINYINT(1) NOT NULL, CHANGE login login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL, CHANGE reset_expiry reset_expiry DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE affiliation affiliation VARCHAR(64) NOT NULL, CHANGE created created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, FULLTEXT INDEX IDX_64C19C16DE44026 (description), FULLTEXT INDEX IDX_64C19C1EA750E8 (label), FULLTEXT INDEX IDX_64C19C1EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE flag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL, updated DATETIME NOT NULL, FULLTEXT INDEX IDX_D1F4EB9A6DE44026 (description), FULLTEXT INDEX IDX_D1F4EB9AEA750E8 (label), FULLTEXT INDEX IDX_D1F4EB9AEA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_D1F4EB9A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE flag_entity (id INT AUTO_INCREMENT NOT NULL, flag_id INT NOT NULL, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_E37C49B9919FE4E5 (flag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE flag_entity ADD CONSTRAINT FK_E37C49B9919FE4E5 FOREIGN KEY (flag_id) REFERENCES flag (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE nines_user CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE reset_token reset_token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE reset_expiry reset_expiry DATETIME DEFAULT NULL, CHANGE affiliation affiliation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE login login DATETIME DEFAULT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_34DCD1763BD1838 ON person');
        $this->addSql('CREATE INDEX IDX_34DCD1763BD1838 ON person (sortable_name(191))');
        $this->addSql('DROP INDEX IDX_AF3C677964C19C1 ON publication');
        $this->addSql('CREATE INDEX IDX_AF3C677964C19C1 ON publication (category(191))');
    }
}
