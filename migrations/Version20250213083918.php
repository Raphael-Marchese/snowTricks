<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250213083918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create role column on user table';
    }

    public function up(Schema $schema): void
    {
        // Étape 1 : Ajouter la colonne "roles" sans contrainte NOT NULL
        $this->addSql('ALTER TABLE "user" ADD roles JSON DEFAULT NULL');

        // Étape 2 : Mettre à jour les lignes existantes avec une valeur par défaut
        $this->addSql('UPDATE "user" SET roles = \'["ROLE_USER"]\' WHERE roles IS NULL');

        // Étape 3 : Modifier la colonne pour ajouter la contrainte NOT NULL
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles SET NOT NULL');

        // Optionnel : Autres modifications
        $this->addSql('ALTER TABLE "user" ALTER is_verified DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP roles');
        $this->addSql('ALTER TABLE "user" ALTER is_verified SET DEFAULT false');
    }
}
