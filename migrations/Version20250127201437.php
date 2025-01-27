<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127201437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_budget ADD namecompte VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transaction DROP date, CHANGE description description VARCHAR(400) NOT NULL, CHANGE compte_id compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F2C56620 FOREIGN KEY (compte_id) REFERENCES compte_budget (id)');
        $this->addSql('CREATE INDEX IDX_723705D1F2C56620 ON transaction (compte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F2C56620');
        $this->addSql('DROP INDEX IDX_723705D1F2C56620 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD date DATETIME DEFAULT NULL, CHANGE compte_id compte_id INT NOT NULL, CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE compte_budget DROP namecompte');
    }
}
