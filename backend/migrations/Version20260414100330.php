<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414100330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant ADD date_creation DATE DEFAULT NULL, ADD actif TINYINT NOT NULL, ADD utilisateur_id_id INT NOT NULL, ADD formation_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT FK_D7A33FE8B981C689 FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT FK_D7A33FE89CF0022 FOREIGN KEY (formation_id_id) REFERENCES formation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7A33FE8B981C689 ON alternant (utilisateur_id_id)');
        $this->addSql('CREATE INDEX IDX_D7A33FE89CF0022 ON alternant (formation_id_id)');
        $this->addSql('ALTER TABLE commentaire ADD date_creation DATE NOT NULL, CHANGE commentaire commentaire VARCHAR(200) NOT NULL, CHANGE fiche_id fiche_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5C47468 FOREIGN KEY (fiche_id_id) REFERENCES fiche (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC5C47468 ON commentaire (fiche_id_id)');
        $this->addSql('ALTER TABLE fiche ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL, ADD date_creation DATE NOT NULL, ADD date_soumission DATE NOT NULL, ADD date_validation DATE NOT NULL, DROP debut_date, DROP fin_date, DROP status');
        $this->addSql('ALTER TABLE formation CHANGE annee_scolaire session VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD date_debut DATE DEFAULT NULL, ADD date_fin DATE DEFAULT NULL, ADD actif TINYINT NOT NULL');
        $this->addSql('ALTER TABLE tache ADD tache_acomplie VARCHAR(250) NOT NULL, ADD date_tache DATE NOT NULL, ADD autonomie TINYINT DEFAULT NULL, ADD observation TINYINT NOT NULL, ADD surveille TINYINT NOT NULL, ADD absence TINYINT NOT NULL, ADD ferie TINYINT NOT NULL, ADD date_creation DATE NOT NULL, DROP description, CHANGE jour jour VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tutorat ADD date_debut DATE DEFAULT NULL, ADD date_fin DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD date_creation DATE DEFAULT NULL, CHANGE nom nom VARCHAR(45) NOT NULL, CHANGE prenom prenom VARCHAR(45) NOT NULL, CHANGE email email VARCHAR(64) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(80) NOT NULL, CHANGE role role VARCHAR(240) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY FK_D7A33FE8B981C689');
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY FK_D7A33FE89CF0022');
        $this->addSql('DROP INDEX UNIQ_D7A33FE8B981C689 ON alternant');
        $this->addSql('DROP INDEX IDX_D7A33FE89CF0022 ON alternant');
        $this->addSql('ALTER TABLE alternant DROP date_creation, DROP actif, DROP utilisateur_id_id, DROP formation_id_id');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5C47468');
        $this->addSql('DROP INDEX IDX_67F068BC5C47468 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP date_creation, CHANGE commentaire commentaire VARCHAR(255) NOT NULL, CHANGE fiche_id_id fiche_id INT NOT NULL');
        $this->addSql('ALTER TABLE fiche ADD debut_date DATE NOT NULL, ADD fin_date DATE NOT NULL, ADD status VARCHAR(10) NOT NULL, DROP date_debut, DROP date_fin, DROP date_creation, DROP date_soumission, DROP date_validation');
        $this->addSql('ALTER TABLE formation CHANGE session annee_scolaire VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP date_debut, DROP date_fin, DROP actif');
        $this->addSql('ALTER TABLE tache ADD description VARCHAR(255) NOT NULL, DROP tache_acomplie, DROP date_tache, DROP autonomie, DROP observation, DROP surveille, DROP absence, DROP ferie, DROP date_creation, CHANGE jour jour VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE tutorat DROP date_debut, DROP date_fin');
        $this->addSql('ALTER TABLE utilisateur DROP date_creation, CHANGE nom nom VARCHAR(40) NOT NULL, CHANGE prenom prenom VARCHAR(40) NOT NULL, CHANGE email email VARCHAR(40) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(40) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL');
    }
}
