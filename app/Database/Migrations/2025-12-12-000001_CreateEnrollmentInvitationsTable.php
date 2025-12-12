<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentInvitationsTable extends Migration
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
                'comment'    => 'Student being invited/requesting'
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['invitation', 'request'],
                'comment'    => 'invitation: admin/teacher invites student, request: student requests enrollment'
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'accepted', 'declined', 'cancelled'],
                'default'    => 'pending'
            ],
            'invited_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Admin/Teacher who sent invitation (null for student requests)'
            ],
            'message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Optional message from sender'
            ],
            'response_message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Optional message when accepting/declining'
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invited_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('enrollment_invitations');
    }

    public function down()
    {
        $this->forge->dropTable('enrollment_invitations');
    }
}
