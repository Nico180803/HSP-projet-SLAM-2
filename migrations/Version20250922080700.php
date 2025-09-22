<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922080700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, ref_user_id INT NOT NULL, ref_commentaire_id INT DEFAULT NULL, ref_sujet_id INT NOT NULL, reponse VARCHAR(255) DEFAULT NULL, pj VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL, INDEX IDX_D9BEC0C4637A8045 (ref_user_id), INDEX IDX_D9BEC0C4F70AE09F (ref_commentaire_id), INDEX IDX_D9BEC0C4FA5A8FF9 (ref_sujet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contacts_entreprise (id INT AUTO_INCREMENT NOT NULL, ref_entreprise_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, INDEX IDX_1DC523DF80FEF88A (ref_entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contacts_entreprise_offres (contacts_entreprise_id INT NOT NULL, offres_id INT NOT NULL, INDEX IDX_60AA3E1AB533F67E (contacts_entreprise_id), INDEX IDX_60AA3E1A6C83CD9F (offres_id), PRIMARY KEY(contacts_entreprise_id, offres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissements (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, tel VARCHAR(255) DEFAULT NULL, nb_rue INT NOT NULL, rue VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, cp VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenements (id INT AUTO_INCREMENT NOT NULL, ref_types_evenement_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, ville VARCHAR(255) NOT NULL, rue VARCHAR(255) DEFAULT NULL, cp VARCHAR(255) DEFAULT NULL, nb_rue VARCHAR(255) DEFAULT NULL, nb_places INT NOT NULL, nb_places_dispo INT NOT NULL, est_valide TINYINT(1) NOT NULL, INDEX IDX_E10AD400904B9C3A (ref_types_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flux (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offres (id INT AUTO_INCREMENT NOT NULL, ref_types_offre_id INT NOT NULL, ref_createur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, date_creation DATETIME NOT NULL, date_fermeture DATETIME DEFAULT NULL, pj VARCHAR(255) DEFAULT NULL, INDEX IDX_C6AC3544CE39867B (ref_types_offre_id), INDEX IDX_C6AC354489ABF969 (ref_createur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offres_user (offres_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F7F284846C83CD9F (offres_id), INDEX IDX_F7F28484A76ED395 (user_id), PRIMARY KEY(offres_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sujets (id INT AUTO_INCREMENT NOT NULL, ref_flux_id INT NOT NULL, ref_user_id INT NOT NULL, message VARCHAR(255) DEFAULT NULL, pj VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL, INDEX IDX_959E33ABC891C1BE (ref_flux_id), INDEX IDX_959E33AB637A8045 (ref_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types_evenements (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types_offres (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, ref_etablissement_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, specialite VARCHAR(255) DEFAULT NULL, cp_entreprise VARCHAR(255) DEFAULT NULL, rue_entreprise VARCHAR(255) DEFAULT NULL, ville_entreprise VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, formation VARCHAR(255) DEFAULT NULL, est_valide TINYINT(1) DEFAULT NULL, date_creation DATETIME NOT NULL, nb_rue_entreprise VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D6492925434B (ref_etablissement_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_evenement (id INT AUTO_INCREMENT NOT NULL, ref_user_id INT NOT NULL, ref_evenement_id INT NOT NULL, is_responsable TINYINT(1) NOT NULL, INDEX IDX_BC6E5FA637A8045 (ref_user_id), INDEX IDX_BC6E5FAEB24E9E0 (ref_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4F70AE09F FOREIGN KEY (ref_commentaire_id) REFERENCES commentaires (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4FA5A8FF9 FOREIGN KEY (ref_sujet_id) REFERENCES sujets (id)');
        $this->addSql('ALTER TABLE contacts_entreprise ADD CONSTRAINT FK_1DC523DF80FEF88A FOREIGN KEY (ref_entreprise_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contacts_entreprise_offres ADD CONSTRAINT FK_60AA3E1AB533F67E FOREIGN KEY (contacts_entreprise_id) REFERENCES contacts_entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contacts_entreprise_offres ADD CONSTRAINT FK_60AA3E1A6C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD400904B9C3A FOREIGN KEY (ref_types_evenement_id) REFERENCES types_evenements (id)');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC3544CE39867B FOREIGN KEY (ref_types_offre_id) REFERENCES types_offres (id)');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC354489ABF969 FOREIGN KEY (ref_createur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE offres_user ADD CONSTRAINT FK_F7F284846C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offres_user ADD CONSTRAINT FK_F7F28484A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sujets ADD CONSTRAINT FK_959E33ABC891C1BE FOREIGN KEY (ref_flux_id) REFERENCES flux (id)');
        $this->addSql('ALTER TABLE sujets ADD CONSTRAINT FK_959E33AB637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492925434B FOREIGN KEY (ref_etablissement_id) REFERENCES etablissements (id)');
        $this->addSql('ALTER TABLE user_evenement ADD CONSTRAINT FK_BC6E5FA637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_evenement ADD CONSTRAINT FK_BC6E5FAEB24E9E0 FOREIGN KEY (ref_evenement_id) REFERENCES evenements (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4637A8045');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4F70AE09F');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4FA5A8FF9');
        $this->addSql('ALTER TABLE contacts_entreprise DROP FOREIGN KEY FK_1DC523DF80FEF88A');
        $this->addSql('ALTER TABLE contacts_entreprise_offres DROP FOREIGN KEY FK_60AA3E1AB533F67E');
        $this->addSql('ALTER TABLE contacts_entreprise_offres DROP FOREIGN KEY FK_60AA3E1A6C83CD9F');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD400904B9C3A');
        $this->addSql('ALTER TABLE offres DROP FOREIGN KEY FK_C6AC3544CE39867B');
        $this->addSql('ALTER TABLE offres DROP FOREIGN KEY FK_C6AC354489ABF969');
        $this->addSql('ALTER TABLE offres_user DROP FOREIGN KEY FK_F7F284846C83CD9F');
        $this->addSql('ALTER TABLE offres_user DROP FOREIGN KEY FK_F7F28484A76ED395');
        $this->addSql('ALTER TABLE sujets DROP FOREIGN KEY FK_959E33ABC891C1BE');
        $this->addSql('ALTER TABLE sujets DROP FOREIGN KEY FK_959E33AB637A8045');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492925434B');
        $this->addSql('ALTER TABLE user_evenement DROP FOREIGN KEY FK_BC6E5FA637A8045');
        $this->addSql('ALTER TABLE user_evenement DROP FOREIGN KEY FK_BC6E5FAEB24E9E0');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE contacts_entreprise');
        $this->addSql('DROP TABLE contacts_entreprise_offres');
        $this->addSql('DROP TABLE etablissements');
        $this->addSql('DROP TABLE evenements');
        $this->addSql('DROP TABLE flux');
        $this->addSql('DROP TABLE offres');
        $this->addSql('DROP TABLE offres_user');
        $this->addSql('DROP TABLE sujets');
        $this->addSql('DROP TABLE types_evenements');
        $this->addSql('DROP TABLE types_offres');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_evenement');
    }
}
