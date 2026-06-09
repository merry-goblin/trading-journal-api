<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Etape 1 du refactoring du modele d'observations.
 *
 * Changements :
 *  1. chart_observation : ADD order_id FK nullable
 *  2. chart_observation : ADD position_id FK nullable
 *  3. chart_observation : trend passe de NOT NULL a NULL
 *  4. screenshot        : observation_id passe de NULL a NOT NULL
 *  5. screenshot        : DROP position_id (remplace par la chaine screenshot→observation→position)
 *  6. DROP TABLE position_chart_observation (relation N:M remplacee par FKs directes)
 *
 * Prerequis : la base de donnees ne doit pas contenir de screenshots
 * avec observation_id = NULL (le changement NOT NULL echouerait sinon).
 */
final class Version20260609120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor observation model: direct FKs on chart_observation, screenshot.observation_id NOT NULL, remove position_chart_observation';
    }

    public function up(Schema $schema): void
    {
        // 1 & 2 — Ajouter order_id et position_id sur chart_observation
        $this->addSql(
            'ALTER TABLE chart_observation
                ADD order_id    INT DEFAULT NULL,
                ADD position_id INT DEFAULT NULL'
        );
        $this->addSql(
            'ALTER TABLE chart_observation
                ADD CONSTRAINT FK_chart_obs_order
                    FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE SET NULL'
        );
        $this->addSql(
            'ALTER TABLE chart_observation
                ADD CONSTRAINT FK_chart_obs_position
                    FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE SET NULL'
        );
        $this->addSql(
            'CREATE INDEX IDX_chart_obs_order    ON chart_observation (order_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_chart_obs_position ON chart_observation (position_id)'
        );

        // 3 — trend : NOT NULL → NULL (observations automatiques sans sentiment initial)
        $this->addSql(
            'ALTER TABLE chart_observation MODIFY trend VARCHAR(25) DEFAULT NULL'
        );

        // 4 — screenshot.observation_id : NULL → NOT NULL
        $this->addSql(
            'ALTER TABLE screenshot MODIFY observation_id INT NOT NULL'
        );

        // 5 — screenshot : DROP position_id
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E41DD842E46');
        $this->addSql('ALTER TABLE screenshot DROP INDEX IDX_58991E41DD842E46');
        $this->addSql('ALTER TABLE screenshot DROP COLUMN position_id');

        // 6 — Supprimer la table de jointure N:M devenue inutile
        $this->addSql('DROP TABLE position_chart_observation');
    }

    public function down(Schema $schema): void
    {
        // Recreer position_chart_observation
        $this->addSql(
            'CREATE TABLE position_chart_observation (
                position_id           INT NOT NULL,
                chart_observation_id  INT NOT NULL,
                PRIMARY KEY (position_id, chart_observation_id),
                INDEX IDX_6474A6A2DD842E46 (position_id),
                INDEX IDX_6474A6A241B0FD98 (chart_observation_id)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        // Restaurer screenshot.position_id
        $this->addSql(
            'ALTER TABLE screenshot ADD position_id INT DEFAULT NULL'
        );
        $this->addSql(
            'ALTER TABLE screenshot MODIFY observation_id INT DEFAULT NULL'
        );

        // Restaurer trend NOT NULL (avec valeur par defaut pour eviter l'echec)
        $this->addSql(
            "UPDATE chart_observation SET trend = 'neutral' WHERE trend IS NULL"
        );
        $this->addSql(
            'ALTER TABLE chart_observation MODIFY trend VARCHAR(25) NOT NULL'
        );

        // Supprimer les colonnes et contraintes ajoutees
        $this->addSql('ALTER TABLE chart_observation DROP FOREIGN KEY FK_chart_obs_order');
        $this->addSql('ALTER TABLE chart_observation DROP FOREIGN KEY FK_chart_obs_position');
        $this->addSql('DROP INDEX IDX_chart_obs_order    ON chart_observation');
        $this->addSql('DROP INDEX IDX_chart_obs_position ON chart_observation');
        $this->addSql('ALTER TABLE chart_observation DROP COLUMN order_id, DROP COLUMN position_id');
    }
}
