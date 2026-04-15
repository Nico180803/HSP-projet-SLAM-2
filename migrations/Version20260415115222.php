<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415115222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0EB24E9E0');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0637A8045');
        $this->addSql('DROP TABLE avis');
        $this->addSql('ALTER TABLE user_evenement ADD statut TINYINT(1) NOT NULL, ADD date_inscription DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, ref_user_id INT DEFAULT NULL, ref_evenement_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, note INT NOT NULL, date DATETIME DEFAULT NULL, INDEX IDX_8F91ABF0637A8045 (ref_user_id), INDEX IDX_8F91ABF0EB24E9E0 (ref_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0EB24E9E0 FOREIGN KEY (ref_evenement_id) REFERENCES evenements (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0637A8045 FOREIGN KEY (ref_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_evenement DROP statut, DROP date_inscription');
    }
}
