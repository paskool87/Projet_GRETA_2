<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409135916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation de table basique';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alternant (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, commentaire VARCHAR(255) NOT NULL, fiche_id INT NOT NULL, auteur_id_id INT NOT NULL, INDEX IDX_67F068BC75F8742E (auteur_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE fiche (id INT AUTO_INCREMENT NOT NULL, debut_date DATE NOT NULL, fin_date DATE NOT NULL, status VARCHAR(10) NOT NULL, alternant_id_id INT NOT NULL, INDEX IDX_4C13CC78FB02512D (alternant_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, nom_formation VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, annee_scolaire VARCHAR(20) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE suivi_pedagogique (id INT AUTO_INCREMENT NOT NULL, est_principal TINYINT NOT NULL, alternant_id_id INT NOT NULL, professeur_id INT DEFAULT NULL, formation_id_id INT DEFAULT NULL, INDEX IDX_8E989E88FB02512D (alternant_id_id), INDEX IDX_8E989E88BAB22EE9 (professeur_id), INDEX IDX_8E989E889CF0022 (formation_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, jour VARCHAR(10) NOT NULL, description VARCHAR(255) NOT NULL, fiche_id_id INT NOT NULL, INDEX IDX_938720755C47468 (fiche_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutorat (id INT AUTO_INCREMENT NOT NULL, actif TINYINT NOT NULL, tuteur_id_id INT NOT NULL, alternant_id_id INT NOT NULL, INDEX IDX_CD484593E143EEC2 (tuteur_id_id), INDEX IDX_CD484593FB02512D (alternant_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(40) NOT NULL, prenom VARCHAR(40) NOT NULL, email VARCHAR(40) NOT NULL, mot_de_passe VARCHAR(40) NOT NULL, actif TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC75F8742E FOREIGN KEY (auteur_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78FB02512D FOREIGN KEY (alternant_id_id) REFERENCES alternant (id)');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT FK_8E989E88FB02512D FOREIGN KEY (alternant_id_id) REFERENCES alternant (id)');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT FK_8E989E88BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT FK_8E989E889CF0022 FOREIGN KEY (formation_id_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720755C47468 FOREIGN KEY (fiche_id_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT FK_CD484593E143EEC2 FOREIGN KEY (tuteur_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT FK_CD484593FB02512D FOREIGN KEY (alternant_id_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC75F8742E');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78FB02512D');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY FK_8E989E88FB02512D');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY FK_8E989E88BAB22EE9');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY FK_8E989E889CF0022');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720755C47468');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY FK_CD484593E143EEC2');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY FK_CD484593FB02512D');
        $this->addSql('DROP TABLE alternant');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE fiche');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE suivi_pedagogique');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE tutorat');
        $this->addSql('DROP TABLE utilisateur');
    }
}
