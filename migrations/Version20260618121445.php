<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260618121445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, texte LONGTEXT NOT NULL, is_anonymous TINYINT NOT NULL, created_at DATETIME NOT NULL, public_user_id INT DEFAULT NULL, conference_id INT NOT NULL, INDEX IDX_9474526CFCB08C27 (public_user_id), INDEX IDX_9474526C604B8382 (conference_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conference (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, lieu VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_911533C87E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE public_user (id INT AUTO_INCREMENT NOT NULL, phone VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFCB08C27 FOREIGN KEY (public_user_id) REFERENCES public_user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id)');
        $this->addSql('ALTER TABLE conference ADD CONSTRAINT FK_911533C87E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFCB08C27');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C604B8382');
        $this->addSql('ALTER TABLE conference DROP FOREIGN KEY FK_911533C87E3C61F9');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE conference');
        $this->addSql('DROP TABLE public_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
