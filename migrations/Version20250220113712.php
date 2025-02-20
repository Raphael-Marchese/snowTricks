<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220113712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F5C011B5');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f5c011b5');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f5c011b5 FOREIGN KEY (figure_id) REFERENCES figure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
