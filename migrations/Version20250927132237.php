<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927132237 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factura (id INT AUTO_INCREMENT NOT NULL, cliente_id INT DEFAULT NULL, local_id INT DEFAULT NULL, numero VARCHAR(40) NOT NULL, fecha DATE NOT NULL, hora VARCHAR(8) NOT NULL, monto NUMERIC(10, 2) NOT NULL, tasa NUMERIC(10, 2) NOT NULL, create_at DATETIME NOT NULL, create_by VARCHAR(100) NOT NULL, update_at DATETIME DEFAULT NULL, update_by VARCHAR(100) DEFAULT NULL, INDEX IDX_F9EBA009DE734E51 (cliente_id), INDEX IDX_F9EBA0095D5A2101 (local_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA0095D5A2101 FOREIGN KEY (local_id) REFERENCES local (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE factura');
    }
}
