<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260101235808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AF5A5CECC836F9 ON asset (symbol)');
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
        $this->addSql('ALTER TABLE screenshot ADD period_start DATETIME NOT NULL, ADD period_end DATETIME NOT NULL, ADD source VARCHAR(10) NOT NULL, ADD asset_id INT NOT NULL, ADD timeframe_id INT NOT NULL');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E415DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E411F6C835C FOREIGN KEY (timeframe_id) REFERENCES timeframe (id)');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E411409DD88 FOREIGN KEY (observation_id) REFERENCES chart_observation (id)');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E41DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('CREATE INDEX IDX_58991E415DA1941 ON screenshot (asset_id)');
        $this->addSql('CREATE INDEX IDX_58991E411F6C835C ON screenshot (timeframe_id)');
        $this->addSql('ALTER TABLE timeframe CHANGE code label VARCHAR(25) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price DOUBLE PRECISION NOT NULL, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX UNIQ_2AF5A5CECC836F9 ON asset');
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
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E415DA1941');
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E411F6C835C');
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E411409DD88');
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E41DD842E46');
        $this->addSql('DROP INDEX IDX_58991E415DA1941 ON screenshot');
        $this->addSql('DROP INDEX IDX_58991E411F6C835C ON screenshot');
        $this->addSql('ALTER TABLE screenshot DROP period_start, DROP period_end, DROP source, DROP asset_id, DROP timeframe_id');
        $this->addSql('ALTER TABLE timeframe CHANGE label code VARCHAR(25) NOT NULL');
    }
}
