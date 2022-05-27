<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527182124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication CHANGE category category VARCHAR(64) NOT NULL');
        $this->addSql('CREATE INDEX IDX_AF3C677964C19C1 ON publication (category)');
        $this->addSql('ALTER TABLE publisher CHANGE created created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(191) NOT NULL, CHANGE label label VARCHAR(200) NOT NULL, CHANGE created created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_AF3C677964C19C1 ON publication');
        $this->addSql('ALTER TABLE publication CHANGE category category VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE publisher CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(120) NOT NULL, CHANGE label label VARCHAR(120) NOT NULL, CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL');
    }
}
