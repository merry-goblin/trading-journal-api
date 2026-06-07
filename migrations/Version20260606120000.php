<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Phase 2 — Ajout de champs d'analyse generique sur la table position.
 *
 * Nouveaux champs :
 *   - plan_respected  : TINYINT(1)  nullable — plan de trading respecte (universel)
 *   - higher_tf_bias  : VARCHAR(10) nullable — biais sur le TF superieur (bull/bear/neutral)
 *   - entry_tf_bias   : VARCHAR(10) nullable — biais sur le TF d'entree (bull/bear/neutral)
 *   - setup_quality   : SMALLINT    nullable — qualite du setup 1-5 (independant de la methode)
 *   - emotion_score   : SMALLINT    nullable — stress ressenti 0-5
 */
final class Version20260606120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add generic analysis fields to position table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE position
                ADD plan_respected  TINYINT(1)   DEFAULT NULL,
                ADD higher_tf_bias  VARCHAR(10)  DEFAULT NULL,
                ADD entry_tf_bias   VARCHAR(10)  DEFAULT NULL,
                ADD setup_quality   SMALLINT     DEFAULT NULL,
                ADD emotion_score   SMALLINT     DEFAULT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE position
                DROP plan_respected,
                DROP higher_tf_bias,
                DROP entry_tf_bias,
                DROP setup_quality,
                DROP emotion_score'
        );
    }
}
