<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseProgramsTable extends Migration
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
            'program_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'program_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'degree_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Bachelor', 'Master', 'Doctorate', 'Certificate', 'Diploma'],
                'default'    => 'Bachelor',
                'null'       => false,
            ],
            'total_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Total units required for graduation',
            ],
            'duration_years' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 4,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('programs', true);
    }

    public function down()
    {
        $this->forge->dropTable('programs', true);
    }
}
