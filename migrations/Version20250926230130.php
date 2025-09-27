<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250926230130 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE local (id INT AUTO_INCREMENT NOT NULL, empresa_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, monto INT NOT NULL, create_at DATETIME NOT NULL, create_by VARCHAR(255) NOT NULL, update_at DATETIME DEFAULT NULL, update_by VARCHAR(255) DEFAULT NULL, INDEX IDX_8BD688E8521E1991 (empresa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE local ADD CONSTRAINT FK_8BD688E8521E1991 FOREIGN KEY (empresa_id) REFERENCES empresa (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE local');
    }
}
