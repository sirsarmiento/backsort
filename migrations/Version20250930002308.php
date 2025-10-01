<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250930002308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factura (id INT AUTO_INCREMENT NOT NULL, local_id INT DEFAULT NULL, user_id INT DEFAULT NULL, numero VARCHAR(40) NOT NULL, fecha DATE NOT NULL, hora VARCHAR(8) NOT NULL, monto NUMERIC(10, 2) NOT NULL, tasa NUMERIC(10, 2) NOT NULL, monto_min NUMERIC(10, 2) NOT NULL, print INT NOT NULL, create_at DATETIME NOT NULL, create_by VARCHAR(100) NOT NULL, update_at DATETIME DEFAULT NULL, update_by VARCHAR(100) DEFAULT NULL, INDEX IDX_F9EBA0095D5A2101 (local_id), INDEX IDX_F9EBA009A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA0095D5A2101 FOREIGN KEY (local_id) REFERENCES local (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE factura');
    }
}
