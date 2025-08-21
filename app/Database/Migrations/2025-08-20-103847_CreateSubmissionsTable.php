<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissionsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quiz_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'answers' => [
                'type' => 'TEXT',
                'null' => true, 
            ],
            'score' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true, 
            ],
            'total_score' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true, 
            ],
            'percentage' => [
                'type'    => 'DECIMAL',
                'constraint' => '5,2', 
                'null'    => true,
            ],
            'attempt_number' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1, 
            ],
            'time_taken_minutes' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true, 
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'quiz_id', 'attempt_number']); // Track multiple attempts
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('submissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('submissions', true);
    }
}
