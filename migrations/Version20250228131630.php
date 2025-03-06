<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250228131630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image table to manage figure images easily';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE figure ADD figure_group_id INT DEFAULT NULL');
        $this->addSql("INSERT INTO category (id, name) VALUES (1, 'Default') ON CONFLICT (id) DO NOTHING");
        $this->addSql('UPDATE figure SET figure_group_id = 1 WHERE figure_group_id IS NULL');
        $this->addSql('ALTER TABLE figure ALTER COLUMN figure_group_id SET NOT NULL');
        $this->addSql('ALTER TABLE figure DROP figure_group');
        $this->addSql('ALTER TABLE figure DROP illustrations');
        $this->addSql('ALTER TABLE figure DROP videos');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AFDE864F2 FOREIGN KEY (figure_group_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2F57B37AFDE864F2 ON figure (figure_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE figure DROP CONSTRAINT FK_2F57B37AFDE864F2');
        $this->addSql('DROP INDEX IDX_2F57B37AFDE864F2');
        $this->addSql('ALTER TABLE figure ADD figure_group VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD illustrations JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD videos JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE figure DROP figure_group_id');
    }
}
