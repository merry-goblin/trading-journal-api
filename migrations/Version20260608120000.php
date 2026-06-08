<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Phase 3 — Suppression du champ stop_price de la table order.
 *
 * Raison : stop_price n'a de sens que pour les ordres Stop Limit,
 * non utilises dans ce plan de trading. Le champ cree de la confusion
 * avec stop_loss qui lui est pertinent.
 */
final class Version20260608120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove stop_price from order table (unused field)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP stop_price');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE `order` ADD stop_price DECIMAL(10, 5) DEFAULT NULL'
        );
    }
}
