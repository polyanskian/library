<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211127202814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Book.date_read set nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER date_read DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER date_read SET NOT NULL');
    }
}
