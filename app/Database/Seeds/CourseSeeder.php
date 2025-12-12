<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Get existing teachers
        $teachers = $this->db->table('users')
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        if (empty($teachers)) {
            echo "No teachers found. Please create teachers first.\n";
            return;
        }

        // Sample courses data
        $courses = [
            // Computer Science Courses
            [
                'course_code' => 'CS101',
                'title' => 'Introduction to Programming',
                'description' => 'Learn the fundamentals of programming using Python. Topics include variables, data types, control structures, functions, and basic algorithms.',
                'units' => 3,
                'year_level' => '1st Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS102',
                'title' => 'Data Structures and Algorithms',
                'description' => 'Study of data structures including arrays, linked lists, stacks, queues, trees, and graphs. Algorithm analysis and design patterns.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS201',
                'title' => 'Object-Oriented Programming',
                'description' => 'Advanced programming concepts using Java. Learn about classes, objects, inheritance, polymorphism, and design patterns.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Computer Science',
                'semester' => '2nd Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS301',
                'title' => 'Database Management Systems',
                'description' => 'Comprehensive study of database design, SQL, normalization, transactions, and database administration using MySQL and PostgreSQL.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS302',
                'title' => 'Software Engineering',
                'description' => 'Software development lifecycle, requirements analysis, system design, testing methodologies, and project management.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Computer Science',
                'semester' => '2nd Semester',
                'lecture_hours' => 3.0,
                'lab_hours' => 2.0,
            ],
            
            // IT Courses
            [
                'course_code' => 'IT101',
                'title' => 'Web Development Fundamentals',
                'description' => 'Learn HTML, CSS, and JavaScript basics. Create responsive websites using modern web technologies and frameworks.',
                'units' => 3,
                'year_level' => '1st Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT102',
                'title' => 'Network Fundamentals',
                'description' => 'Introduction to computer networks, TCP/IP protocols, network architecture, and basic network security concepts.',
                'units' => 3,
                'year_level' => '1st Year',
                'department' => 'Information Technology',
                'semester' => '2nd Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT201',
                'title' => 'Full Stack Web Development',
                'description' => 'Advanced web development using React, Node.js, Express, and MongoDB. Build complete web applications from frontend to backend.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT301',
                'title' => 'Cloud Computing and DevOps',
                'description' => 'Learn AWS, Azure, Docker, Kubernetes, CI/CD pipelines, and modern DevOps practices for cloud infrastructure.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT401',
                'title' => 'Cybersecurity and Ethical Hacking',
                'description' => 'Network security, penetration testing, cryptography, and ethical hacking techniques. Learn to secure systems and applications.',
                'units' => 3,
                'year_level' => '4th Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            
            // Additional Advanced Courses
            [
                'course_code' => 'CS401',
                'title' => 'Artificial Intelligence',
                'description' => 'Machine learning algorithms, neural networks, deep learning, and AI applications. Hands-on projects with TensorFlow and PyTorch.',
                'units' => 3,
                'year_level' => '4th Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS402',
                'title' => 'Mobile App Development',
                'description' => 'Build native and cross-platform mobile applications using React Native and Flutter. iOS and Android development.',
                'units' => 3,
                'year_level' => '4th Year',
                'department' => 'Computer Science',
                'semester' => '2nd Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT302',
                'title' => 'System Administration',
                'description' => 'Linux and Windows server administration, user management, backup strategies, and system monitoring.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Information Technology',
                'semester' => '2nd Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS303',
                'title' => 'Computer Graphics',
                'description' => '2D and 3D graphics programming, rendering techniques, OpenGL, and game development fundamentals.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'IT202',
                'title' => 'Digital Marketing and SEO',
                'description' => 'Learn digital marketing strategies, SEO optimization, social media marketing, and analytics tools.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Information Technology',
                'semester' => '2nd Semester',
                'lecture_hours' => 3.0,
                'lab_hours' => 2.0,
            ],
            [
                'course_code' => 'CS202',
                'title' => 'Discrete Mathematics',
                'description' => 'Mathematical foundations for computer science including logic, set theory, graph theory, and combinatorics.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 3.0,
                'lab_hours' => 0.0,
            ],
            [
                'course_code' => 'IT203',
                'title' => 'UI/UX Design',
                'description' => 'User interface and user experience design principles. Learn Figma, Adobe XD, and modern design thinking.',
                'units' => 3,
                'year_level' => '2nd Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS304',
                'title' => 'Operating Systems',
                'description' => 'Study of operating system concepts: processes, threads, memory management, file systems, and scheduling algorithms.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Computer Science',
                'semester' => '2nd Semester',
                'lecture_hours' => 3.0,
                'lab_hours' => 2.0,
            ],
            [
                'course_code' => 'IT303',
                'title' => 'Internet of Things (IoT)',
                'description' => 'Build IoT applications using Arduino, Raspberry Pi, and cloud platforms. Sensors, actuators, and wireless communication.',
                'units' => 3,
                'year_level' => '3rd Year',
                'department' => 'Information Technology',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
            [
                'course_code' => 'CS403',
                'title' => 'Blockchain and Cryptocurrency',
                'description' => 'Understand blockchain technology, smart contracts, cryptocurrency, and decentralized applications (DApps).',
                'units' => 3,
                'year_level' => '4th Year',
                'department' => 'Computer Science',
                'semester' => '1st Semester',
                'lecture_hours' => 2.0,
                'lab_hours' => 3.0,
            ],
        ];

        // Insert courses with random teacher assignment
        $insertedCount = 0;
        foreach ($courses as $course) {
            // Randomly assign a teacher
            $randomTeacher = $teachers[array_rand($teachers)];
            
            $data = [
                'course_code' => $course['course_code'],
                'title' => $course['title'],
                'description' => $course['description'],
                'units' => $course['units'],
                'year_level' => $course['year_level'],
                'department' => $course['department'],
                'semester' => $course['semester'],
                'lecture_hours' => $course['lecture_hours'],
                'lab_hours' => $course['lab_hours'],
                'teacher_id' => $randomTeacher['id'],
                'status' => 'active',
                'max_students' => rand(25, 40),
                'current_enrolled' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Check if course already exists
            $existing = $this->db->table('courses')
                ->where('course_code', $course['course_code'])
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('courses')->insert($data);
                $insertedCount++;
                echo "✓ Added: {$course['course_code']} - {$course['title']}\n";
            } else {
                echo "⊘ Skipped (exists): {$course['course_code']}\n";
            }
        }

        echo "\n===========================================\n";
        echo "Course Seeder Complete!\n";
        echo "Inserted: {$insertedCount} new courses\n";
        echo "Total courses in database: " . $this->db->table('courses')->countAll() . "\n";
        echo "===========================================\n";
    }
}
