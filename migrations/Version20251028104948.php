<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028104948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement_inscrits (evenements_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A922BE9763C02CD4 (evenements_id), INDEX IDX_A922BE97A76ED395 (user_id), PRIMARY KEY(evenements_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_responsables (evenements_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_291564F363C02CD4 (evenements_id), INDEX IDX_291564F3A76ED395 (user_id), PRIMARY KEY(evenements_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement_inscrits ADD CONSTRAINT FK_A922BE9763C02CD4 FOREIGN KEY (evenements_id) REFERENCES evenements (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_inscrits ADD CONSTRAINT FK_A922BE97A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_responsables ADD CONSTRAINT FK_291564F363C02CD4 FOREIGN KEY (evenements_id) REFERENCES evenements (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_responsables ADD CONSTRAINT FK_291564F3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contacts_entreprise ADD fonction VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_inscrits DROP FOREIGN KEY FK_A922BE9763C02CD4');
        $this->addSql('ALTER TABLE evenement_inscrits DROP FOREIGN KEY FK_A922BE97A76ED395');
        $this->addSql('ALTER TABLE evenement_responsables DROP FOREIGN KEY FK_291564F363C02CD4');
        $this->addSql('ALTER TABLE evenement_responsables DROP FOREIGN KEY FK_291564F3A76ED395');
        $this->addSql('DROP TABLE evenement_inscrits');
        $this->addSql('DROP TABLE evenement_responsables');
        $this->addSql('ALTER TABLE contacts_entreprise DROP fonction');
    }
}
