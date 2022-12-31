<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231094152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chat (id INT NOT NULL, name VARCHAR(50) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE chat_user (chat_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(chat_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2B0F4B081A9A7125 ON chat_user (chat_id)');
        $this->addSql('CREATE INDEX IDX_2B0F4B08A76ED395 ON chat_user (user_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, user_sent_id INT NOT NULL, chat_id_id INT NOT NULL, message TEXT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F1B8777EE ON message (user_sent_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F7E3973CC ON message (chat_id_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, phone_number VARCHAR(20) NOT NULL, country_code VARCHAR(7) NOT NULL, name VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B081A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B08A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1B8777EE FOREIGN KEY (user_sent_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7E3973CC FOREIGN KEY (chat_id_id) REFERENCES chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE chat_user DROP CONSTRAINT FK_2B0F4B081A9A7125');
        $this->addSql('ALTER TABLE chat_user DROP CONSTRAINT FK_2B0F4B08A76ED395');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F1B8777EE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F7E3973CC');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE chat_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE "user"');
    }
}
