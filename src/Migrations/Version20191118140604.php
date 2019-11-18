<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191118140604 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('test1');
        $table->addColumn('id', 'integer')->setUnsigned(true)->setAutoincrement(true);
        $table->addColumn('name', 'string')->setDefault('')->setLength(20);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->hasTable('test1')) {
            $schema->dropTable('test1');
        }
    }
}
