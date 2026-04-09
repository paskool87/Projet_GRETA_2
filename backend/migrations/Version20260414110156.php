<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414110156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache CHANGE observation observation TINYINT DEFAULT NULL, CHANGE surveille surveille TINYINT DEFAULT NULL, CHANGE absence absence TINYINT DEFAULT NULL, CHANGE ferie ferie TINYINT DEFAULT NULL, CHANGE date_creation date_creation DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache CHANGE observation observation TINYINT NOT NULL, CHANGE surveille surveille TINYINT NOT NULL, CHANGE absence absence TINYINT NOT NULL, CHANGE ferie ferie TINYINT NOT NULL, CHANGE date_creation date_creation DATE NOT NULL');
    }
}
