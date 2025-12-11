<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicYearsTable extends Migration
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
            'year_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'unique'     => true,
                'comment'    => 'e.g., 2024-2025',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'current_semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
                'null'       => true,
            ],
            'is_current' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
                'comment' => 'Only one academic year should be current',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['upcoming', 'active', 'completed'],
                'default'    => 'upcoming',
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
        $this->forge->createTable('academic_years', true);
    }

    public function down()
    {
        $this->forge->dropTable('academic_years', true);
    }
}
