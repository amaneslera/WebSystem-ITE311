<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration to add status-based soft delete to users table
 * Adds status column (ENUM: 'active', 'inactive', default 'active')
 * Replaces is_deleted approach with cleaner status field
 */
class AddIsDeletedToUsersTable extends Migration
{
    public function up()
    {
        // Add status column to users table
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
                'comment'    => 'User status: active or inactive'
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
        
        // Add index on status for better query performance
        $this->db->query('ALTER TABLE users ADD INDEX idx_status (status)');
    }

    public function down()
    {
        // Drop index first
        $this->db->query('ALTER TABLE users DROP INDEX IF EXISTS idx_status');
        
        // Drop the status column if it exists
        if ($this->db->fieldExists('status', 'users')) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}
