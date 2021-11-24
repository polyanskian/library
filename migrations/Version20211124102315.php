<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211124102315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table fos_user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE fos_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('CREATE TABLE fos_user (
            id INT NOT NULL, 
            username VARCHAR(180) NOT NULL, 
            username_canonical VARCHAR(180) NOT NULL, 
            email VARCHAR(180) NOT NULL, 
            email_canonical VARCHAR(180) NOT NULL, 
            enabled BOOLEAN NOT NULL, 
            salt VARCHAR(255) DEFAULT NULL, 
            password VARCHAR(255) NOT NULL, 
            last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
            confirmation_token VARCHAR(180) DEFAULT NULL, 
            password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
            roles TEXT NOT NULL, PRIMARY KEY(id)
        )');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479C05FB297 ON fos_user (confirmation_token)');
        $this->addSql('COMMENT ON COLUMN fos_user.roles IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE fos_user_id_seq CASCADE');
        $this->addSql('DROP TABLE fos_user');
    }
}
