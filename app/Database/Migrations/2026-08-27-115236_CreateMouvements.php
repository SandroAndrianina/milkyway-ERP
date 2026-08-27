<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMouvements extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'produit_id' => ['type' => 'INT', 'unsigned' => true],
            'client_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'type' => ['type' => 'ENUM', 'constraint' => ['entree', 'sortie']],
            'cause' => ['type' => 'ENUM', 'constraint' => ['vente', 'non_conforme'], 'null' => true],
            'quantite' => ['type' => 'INT', 'unsigned' => true],
            'date_mouvement' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produit_id', 'produits', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('mouvements');
    }

    public function down()
    {
        $this->forge->dropTable('mouvements');
    }
}