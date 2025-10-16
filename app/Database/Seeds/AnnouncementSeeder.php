<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the Portal',
                'content' => 'We are excited to launch our new student portal!',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Exam Schedule Released',
                'content' => 'Check the portal for your upcoming exam dates.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
