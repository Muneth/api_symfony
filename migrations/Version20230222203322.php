<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222203322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture ADD personne_id INT NOT NULL');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FA21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id)');
        $this->addSql('CREATE INDEX IDX_E9E2810FA21BD112 ON voiture (personne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FA21BD112');
        $this->addSql('DROP INDEX IDX_E9E2810FA21BD112 ON voiture');
        $this->addSql('ALTER TABLE voiture DROP personne_id');
    }
}
