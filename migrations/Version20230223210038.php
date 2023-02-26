<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223210038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, villedepart_id INT NOT NULL, villearrive_id INT NOT NULL, date DATE NOT NULL, kms INT NOT NULL, INDEX IDX_2B5BA98C42235125 (villedepart_id), INDEX IDX_2B5BA98C18232926 (villearrive_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C42235125 FOREIGN KEY (villedepart_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C18232926 FOREIGN KEY (villearrive_id) REFERENCES ville (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C42235125');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C18232926');
        $this->addSql('DROP TABLE trajet');
    }
}
