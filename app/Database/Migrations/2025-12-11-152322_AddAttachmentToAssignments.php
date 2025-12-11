<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttachmentToAssignments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assignments', [
            'attachment_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'description'
            ],
            'attachment_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'attachment_file'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', ['attachment_file', 'attachment_path']);
    }
}
