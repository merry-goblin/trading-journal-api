<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260617120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout is_backtest sur order, position et tag';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` ADD is_backtest TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE position ADD is_backtest TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE tag ADD is_backtest TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP is_backtest');
        $this->addSql('ALTER TABLE position DROP is_backtest');
        $this->addSql('ALTER TABLE tag DROP is_backtest');
    }
}
