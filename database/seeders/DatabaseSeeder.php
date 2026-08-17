<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Tài khoản mẫu
        $admin = User::firstOrCreate(
            ['email' => 'admin@tlu.edu.vn'],
            [
                'name' => 'Cán Bộ Đoàn Trường',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'student_code' => 'ADMIN001'
            ]
        );

        $manager1 = User::firstOrCreate(
            ['email' => 'it.cn@tlu.edu.vn'],
            [
                'name' => 'Nguyễn Văn Tiến (Chủ nhiệm IT)',
                'password' => Hash::make('123456'),
                'role' => 'club_manager',
                'student_code' => 'A35001'
            ]
        );

        $manager2 = User::firstOrCreate(
            ['email' => 'music.cn@tlu.edu.vn'],
            [
                'name' => 'Trần Thị Thu (Chủ nhiệm Âm Nhạc)',
                'password' => Hash::make('123456'),
                'role' => 'club_manager',
                'student_code' => 'A35002'
            ]
        );

        $student1 = User::firstOrCreate(
            ['email' => 'student1@tlu.edu.vn'],
            [
                'name' => 'Lê Hoàng Nam',
                'password' => Hash::make('123456'),
                'role' => 'student',
                'student_code' => 'A36101'
            ]
        );

        $student2 = User::firstOrCreate(
            ['email' => 'student2@tlu.edu.vn'],
            [
                'name' => 'Phạm Minh Anh',
                'password' => Hash::make('123456'),
                'role' => 'student',
                'student_code' => 'A36102'
            ]
        );

        // 2. Tạo Câu lạc bộ mẫu
        $clubIT = Club::firstOrCreate(
            ['name' => 'CLB Công Nghệ Thông Tin (CSE)'],
            [
                'code' => 'CLB-CSE',
                'description' => 'CLB chuyên môn học thuật về lập trình, trí tuệ nhân tạo và phát triển phần mềm.',
                'status' => 'active'
            ]
        );

        $clubMusic = Club::firstOrCreate(
            ['name' => 'CLB Âm Nhạc TLU (TMC)'],
            [
                'code' => 'CLB-TMC',
                'description' => 'CLB phong trào âm nhạc, guitar, ca hát và tổ chức sự kiện văn nghệ.',
                'status' => 'active'
            ]
        );

        // 3. Gán thành viên và chủ nhiệm vào CLB
        ClubMember::firstOrCreate(
            ['club_id' => $clubIT->id, 'user_id' => $manager1->id],
            ['role' => 'leader', 'is_manager' => true]
        );

        ClubMember::firstOrCreate(
            ['club_id' => $clubIT->id, 'user_id' => $student1->id],
            ['role' => 'member', 'is_manager' => false]
        );

        ClubMember::firstOrCreate(
            ['club_id' => $clubMusic->id, 'user_id' => $manager2->id],
            ['role' => 'leader', 'is_manager' => true]
        );

        ClubMember::firstOrCreate(
            ['club_id' => $clubMusic->id, 'user_id' => $student2->id],
            ['role' => 'member', 'is_manager' => false]
        );

        // 4. Tạo Danh mục sự kiện mẫu
        $catAcad = EventCategory::firstOrCreate(
            ['name' => 'Học thuật & Trí tuệ'],
            ['description' => 'Hội thảo chuyên môn, Cuộc thi lập trình, Hackathon và Workshop kiến thức.']
        );

        $catMusic = EventCategory::firstOrCreate(
            ['name' => 'Văn nghệ & Giải trí'],
            ['description' => 'Đêm nhạc sinh viên, Giao lưu văn hóa, Cuộc thi tài năng.']
        );

        EventCategory::firstOrCreate(
            ['name' => 'Thể thao & Sức khỏe'],
            ['description' => 'Giải bóng đá sinh viên, Hội thao trường, Chạy việt dã.']
        );

        EventCategory::firstOrCreate(
            ['name' => 'Tình nguyện & Xã hội'],
            ['description' => 'Mùa hè xanh, Tiếp sức mùa thi, Hiến máu nhân đạo.']
        );

        // 5. Tạo Sự kiện mẫu (Events)
        \App\Models\Event::firstOrCreate(
            ['name' => 'Cuộc thi Lập trình sinh viên TLU 2026'],
            [
                'club_id' => $clubIT->id,
                'category_id' => $catAcad->id,
                'description' => 'Cuộc thi lập trình thuật toán dành cho sinh viên với nhiều giải thưởng hấp dẫn.',
                'location' => 'Hội trường 1',
                'capacity' => 100,
                'start_time' => now()->addDays(5)->setTime(8, 0),
                'end_time' => now()->addDays(5)->setTime(12, 0),
                'status' => 'approved'
            ]
        );

        \App\Models\Event::firstOrCreate(
            ['name' => 'Đêm nhạc Acoustic Chào Tân Sinh Viên'],
            [
                'club_id' => $clubMusic->id,
                'category_id' => $catMusic->id,
                'description' => 'Đêm nhạc giao lưu chào đón tân sinh viên khóa mới.',
                'location' => 'Sân vận động',
                'capacity' => 300,
                'start_time' => now()->addDays(10)->setTime(19, 0),
                'end_time' => now()->addDays(10)->setTime(22, 0),
                'status' => 'approved'
            ]
        );
    }
}
