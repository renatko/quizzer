<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240803135910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE test_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE test_question_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE test_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE test_result_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE test_question (id INT NOT NULL, question TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE test_question_answer (
          id INT NOT NULL,
          question_id INT NOT NULL,
          answer TEXT NOT NULL,
          is_correct BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_A34E55681E27F6BF ON test_question_answer (question_id)');
        $this->addSql('CREATE TABLE test_result (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE test_result_answer (
          id INT NOT NULL,
          question_answer_id INT NOT NULL,
          test_result_id INT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_18B7C735A3E60C9C ON test_result_answer (question_answer_id)');
        $this->addSql('CREATE INDEX IDX_18B7C735853A2189 ON test_result_answer (test_result_id)');
        $this->addSql('CREATE TABLE messenger_messages (
          id BIGSERIAL NOT NULL,
          body TEXT NOT NULL,
          headers TEXT NOT NULL,
          queue_name VARCHAR(190) NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE
        OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$ BEGIN PERFORM pg_notify(
          \'messenger_messages\', NEW.queue_name :: text
        ); RETURN NEW; END; $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT
        OR
        UPDATE
          ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE
          test_question_answer
        ADD
          CONSTRAINT FK_A34E55681E27F6BF FOREIGN KEY (question_id) REFERENCES test_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          test_result_answer
        ADD
          CONSTRAINT FK_18B7C735A3E60C9C FOREIGN KEY (question_answer_id) REFERENCES test_question_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          test_result_answer
        ADD
          CONSTRAINT FK_18B7C735853A2189 FOREIGN KEY (test_result_id) REFERENCES test_result (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE test_question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE test_question_answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE test_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE test_result_answer_id_seq CASCADE');
        $this->addSql('ALTER TABLE test_question_answer DROP CONSTRAINT FK_A34E55681E27F6BF');
        $this->addSql('ALTER TABLE test_result_answer DROP CONSTRAINT FK_18B7C735A3E60C9C');
        $this->addSql('ALTER TABLE test_result_answer DROP CONSTRAINT FK_18B7C735853A2189');
        $this->addSql('DROP TABLE test_question');
        $this->addSql('DROP TABLE test_question_answer');
        $this->addSql('DROP TABLE test_result');
        $this->addSql('DROP TABLE test_result_answer');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
