<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028102246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, ingredients_id INT NOT NULL, steps_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_DA88B1373EC4DCE (ingredients_id), INDEX IDX_DA88B1371EBBD054 (steps_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE step (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1373EC4DCE FOREIGN KEY (ingredients_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1371EBBD054 FOREIGN KEY (steps_id) REFERENCES step (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1373EC4DCE');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1371EBBD054');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE step');
    }
}
