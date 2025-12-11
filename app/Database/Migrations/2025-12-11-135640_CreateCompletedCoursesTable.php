<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompletedCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'completed_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'grade' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'institution' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'For transfer students - institution where course was completed',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'course_id']); // Prevent duplicate entries
        $this->forge->createTable('completed_courses');
    }

    public function down()
    {
        $this->forge->dropTable('completed_courses');
    }
}
