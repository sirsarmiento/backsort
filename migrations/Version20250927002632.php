<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927002632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, estado_id INT DEFAULT NULL, ciudad_id INT DEFAULT NULL, tipo_documento_identidad VARCHAR(1) NOT NULL, nro_documento_identidad VARCHAR(15) NOT NULL, primer_nombre VARCHAR(50) NOT NULL, segundo_nombre VARCHAR(50) DEFAULT NULL, primer_apellido VARCHAR(50) NOT NULL, segundo_apellido VARCHAR(50) DEFAULT NULL, email VARCHAR(100) NOT NULL, cod_telefono VARCHAR(4) NOT NULL, nro_telefono VARCHAR(50) NOT NULL, direccion VARCHAR(1000) DEFAULT NULL, create_at DATETIME NOT NULL, create_by VARCHAR(100) NOT NULL, update_at DATETIME DEFAULT NULL, update_by VARCHAR(100) DEFAULT NULL, INDEX IDX_F41C9B259F5A440B (estado_id), INDEX IDX_F41C9B25E8608214 (ciudad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B259F5A440B FOREIGN KEY (estado_id) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25E8608214 FOREIGN KEY (ciudad_id) REFERENCES ciudad (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cliente');
    }
}
