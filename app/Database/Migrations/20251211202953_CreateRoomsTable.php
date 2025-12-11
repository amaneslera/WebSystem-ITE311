<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'room_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'building' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 30,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['lecture', 'laboratory', 'seminar', 'auditorium', 'other'],
                'default'    => 'lecture',
                'null'       => false,
            ],
            'equipment' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Available equipment (projector, whiteboard, computers, etc)',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['available', 'maintenance', 'unavailable'],
                'default'    => 'available',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['room_number', 'building']);
        $this->forge->createTable('rooms', true);
    }

    public function down()
    {
        $this->forge->dropTable('rooms', true);
    }
}
