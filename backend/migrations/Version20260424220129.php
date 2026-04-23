<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424220129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY `FK_D7A33FE89CF0022`');
        $this->addSql('DROP INDEX IDX_D7A33FE89CF0022 ON alternant');
        $this->addSql('ALTER TABLE alternant CHANGE formation_id_id formation_id INT NOT NULL');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT FK_D7A33FE85200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_D7A33FE85200282E ON alternant (formation_id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY `FK_67F068BC5C47468`');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY `FK_67F068BC75F8742E`');
        $this->addSql('DROP INDEX IDX_67F068BC5C47468 ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC75F8742E ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD fiche_id INT NOT NULL, ADD auteur_id INT DEFAULT NULL, DROP fiche_id_id, DROP auteur_id_id');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCDF522508 ON commentaire (fiche_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC60BB6FE6 ON commentaire (auteur_id)');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY `FK_4C13CC78FB02512D`');
        $this->addSql('DROP INDEX IDX_4C13CC78FB02512D ON fiche');
        $this->addSql('ALTER TABLE fiche ADD alternant_id INT DEFAULT NULL, DROP alternant_id_id, CHANGE date_creation date_creation DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78D6B19C91 FOREIGN KEY (alternant_id) REFERENCES alternant (id)');
        $this->addSql('CREATE INDEX IDX_4C13CC78D6B19C91 ON fiche (alternant_id)');
        $this->addSql('ALTER TABLE formation CHANGE nom_formation nom_formation VARCHAR(45) NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE session session VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY `FK_8E989E889CF0022`');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY `FK_8E989E88FB02512D`');
        $this->addSql('DROP INDEX IDX_8E989E889CF0022 ON suivi_pedagogique');
        $this->addSql('DROP INDEX IDX_8E989E88FB02512D ON suivi_pedagogique');
        $this->addSql('ALTER TABLE suivi_pedagogique CHANGE alternant_id_id alternant_id INT NOT NULL, CHANGE formation_id_id formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT FK_8E989E88D6B19C91 FOREIGN KEY (alternant_id) REFERENCES alternant (id)');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT FK_8E989E885200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_8E989E88D6B19C91 ON suivi_pedagogique (alternant_id)');
        $this->addSql('CREATE INDEX IDX_8E989E885200282E ON suivi_pedagogique (formation_id)');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY `FK_938720755C47468`');
        $this->addSql('DROP INDEX IDX_938720755C47468 ON tache');
        $this->addSql('ALTER TABLE tache ADD fiche_id INT DEFAULT NULL, DROP fiche_id_id, CHANGE tache_acomplie description VARCHAR(250) NOT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('CREATE INDEX IDX_93872075DF522508 ON tache (fiche_id)');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY `FK_CD484593E143EEC2`');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY `FK_CD484593FB02512D`');
        $this->addSql('DROP INDEX IDX_CD484593E143EEC2 ON tutorat');
        $this->addSql('DROP INDEX IDX_CD484593FB02512D ON tutorat');
        $this->addSql('ALTER TABLE tutorat ADD tuteur_id INT NOT NULL, ADD alternant_id INT NOT NULL, DROP tuteur_id_id, DROP alternant_id_id');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT FK_CD48459386EC68D8 FOREIGN KEY (tuteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT FK_CD484593D6B19C91 FOREIGN KEY (alternant_id) REFERENCES alternant (id)');
        $this->addSql('CREATE INDEX IDX_CD48459386EC68D8 ON tutorat (tuteur_id)');
        $this->addSql('CREATE INDEX IDX_CD484593D6B19C91 ON tutorat (alternant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternant DROP FOREIGN KEY FK_D7A33FE85200282E');
        $this->addSql('DROP INDEX IDX_D7A33FE85200282E ON alternant');
        $this->addSql('ALTER TABLE alternant CHANGE formation_id formation_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE alternant ADD CONSTRAINT `FK_D7A33FE89CF0022` FOREIGN KEY (formation_id_id) REFERENCES formation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D7A33FE89CF0022 ON alternant (formation_id_id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCDF522508');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('DROP INDEX IDX_67F068BCDF522508 ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC60BB6FE6 ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD auteur_id_id INT NOT NULL, DROP auteur_id, CHANGE fiche_id fiche_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT `FK_67F068BC5C47468` FOREIGN KEY (fiche_id_id) REFERENCES fiche (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT `FK_67F068BC75F8742E` FOREIGN KEY (auteur_id_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_67F068BC5C47468 ON commentaire (fiche_id_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC75F8742E ON commentaire (auteur_id_id)');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78D6B19C91');
        $this->addSql('DROP INDEX IDX_4C13CC78D6B19C91 ON fiche');
        $this->addSql('ALTER TABLE fiche ADD alternant_id_id INT NOT NULL, DROP alternant_id, CHANGE date_creation date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT `FK_4C13CC78FB02512D` FOREIGN KEY (alternant_id_id) REFERENCES alternant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4C13CC78FB02512D ON fiche (alternant_id_id)');
        $this->addSql('ALTER TABLE formation CHANGE nom_formation nom_formation VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE session session VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY FK_8E989E88D6B19C91');
        $this->addSql('ALTER TABLE suivi_pedagogique DROP FOREIGN KEY FK_8E989E885200282E');
        $this->addSql('DROP INDEX IDX_8E989E88D6B19C91 ON suivi_pedagogique');
        $this->addSql('DROP INDEX IDX_8E989E885200282E ON suivi_pedagogique');
        $this->addSql('ALTER TABLE suivi_pedagogique CHANGE alternant_id alternant_id_id INT NOT NULL, CHANGE formation_id formation_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT `FK_8E989E889CF0022` FOREIGN KEY (formation_id_id) REFERENCES formation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE suivi_pedagogique ADD CONSTRAINT `FK_8E989E88FB02512D` FOREIGN KEY (alternant_id_id) REFERENCES alternant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8E989E889CF0022 ON suivi_pedagogique (formation_id_id)');
        $this->addSql('CREATE INDEX IDX_8E989E88FB02512D ON suivi_pedagogique (alternant_id_id)');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075DF522508');
        $this->addSql('DROP INDEX IDX_93872075DF522508 ON tache');
        $this->addSql('ALTER TABLE tache ADD fiche_id_id INT NOT NULL, DROP fiche_id, CHANGE description tache_acomplie VARCHAR(250) NOT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT `FK_938720755C47468` FOREIGN KEY (fiche_id_id) REFERENCES fiche (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_938720755C47468 ON tache (fiche_id_id)');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY FK_CD48459386EC68D8');
        $this->addSql('ALTER TABLE tutorat DROP FOREIGN KEY FK_CD484593D6B19C91');
        $this->addSql('DROP INDEX IDX_CD48459386EC68D8 ON tutorat');
        $this->addSql('DROP INDEX IDX_CD484593D6B19C91 ON tutorat');
        $this->addSql('ALTER TABLE tutorat ADD tuteur_id_id INT NOT NULL, ADD alternant_id_id INT NOT NULL, DROP tuteur_id, DROP alternant_id');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT `FK_CD484593E143EEC2` FOREIGN KEY (tuteur_id_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE tutorat ADD CONSTRAINT `FK_CD484593FB02512D` FOREIGN KEY (alternant_id_id) REFERENCES alternant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CD484593E143EEC2 ON tutorat (tuteur_id_id)');
        $this->addSql('CREATE INDEX IDX_CD484593FB02512D ON tutorat (alternant_id_id)');
    }
}
