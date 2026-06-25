<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260618120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation de la table daily_session';
    }

    public function up(Schema $schema): void
    {
        // Note : pas de FOREIGN KEY vers asset — Doctrine ORM gere la relation
        // au niveau PHP. La contrainte DB n'est pas necessaire au fonctionnement.
        $this->addSql('
            CREATE TABLE daily_session (
                id                 INT          AUTO_INCREMENT NOT NULL,
                asset_id           INT          DEFAULT NULL,
                date               DATE         NOT NULL,
                pre_bias           VARCHAR(10)  DEFAULT NULL,
                pre_key_levels     LONGTEXT     DEFAULT NULL,
                pre_analysis       LONGTEXT     DEFAULT NULL,
                intra_notes        LONGTEXT     DEFAULT NULL,
                post_review        LONGTEXT     DEFAULT NULL,
                post_emotion_score SMALLINT     DEFAULT NULL,
                post_discipline    TINYINT(1)   DEFAULT NULL,
                created_at         DATETIME     NOT NULL,
                updated_at         DATETIME     NOT NULL,
                UNIQUE INDEX UNIQ_DS_DATE (date),
                INDEX IDX_DS_ASSET (asset_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE utf8mb4_unicode_ci
              ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE daily_session');
    }
}