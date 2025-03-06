<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250228125737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image table to manage figure images easily';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE image (id SERIAL NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE image');
    }
}
