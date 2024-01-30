<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130073031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3E2E969B');
        // $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        // $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFFDF7169');
        // $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        // $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        // $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES comment (id)');
        // $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B33E2E969B');
        // $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        // $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B33E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        // $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        // $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C661220EA6');
        $this->addSql('ALTER TABLE review ADD creation_date DATETIME NOT NULL');
        // $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C661220EA6');
        $this->addSql('ALTER TABLE review DROP creation_date');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B33E2E969B');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B33E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3E2E969B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFFDF7169');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES comment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
