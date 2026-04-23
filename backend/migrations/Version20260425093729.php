<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260425093729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY `FK_D7A33FE8B981C689`');
        $this->addSql('DROP INDEX UNIQ_D7A33FE8B981C689 ON alternant');
        $this->addSql('ALTER TABLE alternant CHANGE utilisateur_id_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT FK_D7A33FE8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7A33FE8FB88E14F ON alternant (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY FK_D7A33FE8FB88E14F');
        $this->addSql('DROP INDEX UNIQ_D7A33FE8FB88E14F ON alternant');
        $this->addSql('ALTER TABLE alternant CHANGE utilisateur_id utilisateur_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT `FK_D7A33FE8B981C689` FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7A33FE8B981C689 ON alternant (utilisateur_id_id)');
    }
}
