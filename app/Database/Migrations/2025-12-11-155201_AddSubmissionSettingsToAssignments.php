<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubmissionSettingsToAssignments extends Migration
{
    public function up()
    {
        $fields = [
            'max_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'comment' => 'Maximum number of submission attempts allowed',
                'after' => 'allow_late_submission'
            ],
            'extended_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Extended deadline for late submissions',
                'after' => 'max_attempts'
            ],
        ];
        
        $this->forge->addColumn('assignments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', ['max_attempts', 'extended_deadline']);
    }
}
