<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410090446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche ADD status_fiche VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY `FK_CD484593FB02512D`');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT FK_CD484593FB02512D FOREIGN KEY (alternant_id_id) REFERENCES alternant (id)');
        $this->addSql('ALTER TABLE utilisateur ADD role VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche DROP status_fiche');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY FK_CD484593FB02512D');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT `FK_CD484593FB02512D` FOREIGN KEY (alternant_id_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE utilisateur DROP role');
    }
}
