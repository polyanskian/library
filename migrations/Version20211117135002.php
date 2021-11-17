<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211117135002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table book';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('CREATE TABLE book (
            id INT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            author VARCHAR(255) NOT NULL, 
            cover VARCHAR(255) DEFAULT NULL, 
            file VARCHAR(255) DEFAULT NULL, 
            date_read TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            is_download BOOLEAN DEFAULT \'false\' NOT NULL, 
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE UNIQUE INDEX name_author ON book (name, author)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('DROP TABLE book');
    }
}
