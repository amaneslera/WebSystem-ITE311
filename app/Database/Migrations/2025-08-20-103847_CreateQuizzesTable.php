<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzesTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'lesson_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'questions' => [
                'type' => 'TEXT',
                'null' => true, 
            ],
            'total_points' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 100, 
            ],
            'time_limit_minutes' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true, 
            ],
            'attempts_allowed' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1, 
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true, 
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
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quizzes', true);
    }

    public function down()
    {
        $this->forge->dropTable('quizzes', true);
    }
}
