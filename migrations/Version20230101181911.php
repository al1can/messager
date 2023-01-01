<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230101181911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE recipient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE recipient (id INT NOT NULL, message_id INT DEFAULT NULL, recipient_user_id INT DEFAULT NULL, recipient_group_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6804FB49537A1329 ON recipient (message_id)');
        $this->addSql('CREATE INDEX IDX_6804FB49B15EFB97 ON recipient (recipient_user_id)');
        $this->addSql('CREATE INDEX IDX_6804FB49104C8843 ON recipient (recipient_group_id)');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB49537A1329 FOREIGN KEY (message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB49B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB49104C8843 FOREIGN KEY (recipient_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message RENAME COLUMN date_time TO create_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE recipient_id_seq CASCADE');
        $this->addSql('ALTER TABLE recipient DROP CONSTRAINT FK_6804FB49537A1329');
        $this->addSql('ALTER TABLE recipient DROP CONSTRAINT FK_6804FB49B15EFB97');
        $this->addSql('ALTER TABLE recipient DROP CONSTRAINT FK_6804FB49104C8843');
        $this->addSql('DROP TABLE recipient');
        $this->addSql('ALTER TABLE message RENAME COLUMN create_date TO date_time');
    }
}
