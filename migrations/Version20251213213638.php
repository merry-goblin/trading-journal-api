<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251213213638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, symbol VARCHAR(50) NOT NULL, type VARCHAR(50) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE chart_observation (id INT AUTO_INCREMENT NOT NULL, observed_at DATETIME NOT NULL, trend VARCHAR(25) NOT NULL, comment LONGTEXT DEFAULT NULL, asset_id INT NOT NULL, timeframe_id INT NOT NULL, INDEX IDX_C955BF7F5DA1941 (asset_id), INDEX IDX_C955BF7F1F6C835C (timeframe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, order_type VARCHAR(15) NOT NULL, direction VARCHAR(5) NOT NULL, price NUMERIC(10, 5) DEFAULT NULL, stop_price NUMERIC(10, 5) DEFAULT NULL, size NUMERIC(5, 2) NOT NULL, stop_loss NUMERIC(10, 5) DEFAULT NULL, take_profit NUMERIC(10, 5) DEFAULT NULL, status VARCHAR(15) NOT NULL, comment LONGTEXT DEFAULT NULL, asset_id INT NOT NULL, timeframe_id INT NOT NULL, INDEX IDX_F52993985DA1941 (asset_id), INDEX IDX_F52993981F6C835C (timeframe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, opened_at DATETIME NOT NULL, closed_at DATETIME DEFAULT NULL, direction VARCHAR(5) DEFAULT NULL, entry_price NUMERIC(10, 5) NOT NULL, exit_price NUMERIC(10, 5) DEFAULT NULL, stop_loss NUMERIC(10, 5) DEFAULT NULL, take_profit NUMERIC(10, 5) DEFAULT NULL, volume NUMERIC(5, 2) NOT NULL, risk_amount NUMERIC(10, 2) DEFAULT NULL, pnl NUMERIC(10, 2) DEFAULT NULL, pnl_percent NUMERIC(5, 2) DEFAULT NULL, rr NUMERIC(5, 2) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, asset_id INT NOT NULL, timeframe_id INT NOT NULL, origin_order_id INT DEFAULT NULL, INDEX IDX_462CE4F55DA1941 (asset_id), INDEX IDX_462CE4F51F6C835C (timeframe_id), UNIQUE INDEX UNIQ_462CE4F5BE2907C8 (origin_order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE position_chart_observation (position_id INT NOT NULL, chart_observation_id INT NOT NULL, INDEX IDX_6474A6A2DD842E46 (position_id), INDEX IDX_6474A6A241B0FD98 (chart_observation_id), PRIMARY KEY (position_id, chart_observation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE position_tag (position_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F73FBF9BDD842E46 (position_id), INDEX IDX_F73FBF9BBAD26311 (tag_id), PRIMARY KEY (position_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE screenshot (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, observation_id INT DEFAULT NULL, position_id INT DEFAULT NULL, INDEX IDX_58991E411409DD88 (observation_id), INDEX IDX_58991E41DD842E46 (position_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE timeframe (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(25) NOT NULL, seconds INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE chart_observation ADD CONSTRAINT FK_C955BF7F5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE chart_observation ADD CONSTRAINT FK_C955BF7F1F6C835C FOREIGN KEY (timeframe_id) REFERENCES timeframe (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993985DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993981F6C835C FOREIGN KEY (timeframe_id) REFERENCES timeframe (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F55DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F51F6C835C FOREIGN KEY (timeframe_id) REFERENCES timeframe (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5BE2907C8 FOREIGN KEY (origin_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE position_chart_observation ADD CONSTRAINT FK_6474A6A2DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_chart_observation ADD CONSTRAINT FK_6474A6A241B0FD98 FOREIGN KEY (chart_observation_id) REFERENCES chart_observation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_tag ADD CONSTRAINT FK_F73FBF9BDD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_tag ADD CONSTRAINT FK_F73FBF9BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E411409DD88 FOREIGN KEY (observation_id) REFERENCES chart_observation (id)');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E41DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chart_observation DROP FOREIGN KEY FK_C955BF7F5DA1941');
        $this->addSql('ALTER TABLE chart_observation DROP FOREIGN KEY FK_C955BF7F1F6C835C');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993985DA1941');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993981F6C835C');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F55DA1941');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F51F6C835C');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5BE2907C8');
        $this->addSql('ALTER TABLE position_chart_observation DROP FOREIGN KEY FK_6474A6A2DD842E46');
        $this->addSql('ALTER TABLE position_chart_observation DROP FOREIGN KEY FK_6474A6A241B0FD98');
        $this->addSql('ALTER TABLE position_tag DROP FOREIGN KEY FK_F73FBF9BDD842E46');
        $this->addSql('ALTER TABLE position_tag DROP FOREIGN KEY FK_F73FBF9BBAD26311');
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E411409DD88');
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E41DD842E46');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE chart_observation');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE position_chart_observation');
        $this->addSql('DROP TABLE position_tag');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE screenshot');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE timeframe');
    }
}
