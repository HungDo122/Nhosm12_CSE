<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Users
        $admin = \App\Models\User::create([
            'name' => 'Admin Hệ Thống',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $manager = \App\Models\User::create([
            'name' => 'Nguyễn Chủ Nhiệm',
            'email' => 'manager@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'club_manager',
        ]);

        $student = \App\Models\User::create([
            'name' => 'Trần Sinh Viên',
            'email' => 'student@gmail.com',
            'student_code' => 'SV123456',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $student2 = \App\Models\User::create([
            'name' => 'Lê Đăng Ký Trễ',
            'email' => 'student2@gmail.com',
            'student_code' => 'SV654321',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // 2. Tạo Câu lạc bộ
        $club = \App\Models\Club::create([
            'name' => 'CLB Lập Trình CSE',
            'description' => 'Câu lạc bộ dành cho những người yêu code.',
            'manager_id' => $manager->id,
        ]);

        // Thêm member
        \App\Models\ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $manager->id,
            'role' => 'leader'
        ]);

        // 3. Tạo Loại Sự kiện
        $category = \App\Models\EventCategory::create([
            'name' => 'Hội thảo Công nghệ'
        ]);

        // 4. Tạo Sự kiện
        \App\Models\Event::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'Tech Talk 2026: AI & Tương lai',
            'description' => 'Một buổi thảo luận hấp dẫn về Trí tuệ nhân tạo. Hãy tham gia để nhận ngay những phần quà hấp dẫn và cộng 5 điểm rèn luyện.',
            'location' => 'Hội trường A, Đại học XYZ',
            'capacity' => 50,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(4),
            'status' => 'approved',
        ]);

        // Sự kiện thứ 2 (để test giới hạn chỗ)
        \App\Models\Event::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'Workshop Code Siêu Tốc',
            'description' => 'Workshop thực hành code cực nhanh trong 1 giờ. Sự kiện này chỉ có đúng 1 slot (Để bạn test tính năng báo Hết Chỗ).',
            'location' => 'Phòng Lab 102',
            'capacity' => 1,
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addHours(2),
            'status' => 'approved',
        ]);
    }
}
