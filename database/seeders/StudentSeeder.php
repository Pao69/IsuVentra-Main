<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // 1. Create 20 normal users + linked students
        User::factory()
            ->count(20)
            ->create()
            ->each(function ($user) {
                Student::factory()->create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                ]);
            });

        // 2. Optional: create a separate admin user + linked student
        $adminUser = User::factory()->admin()->create();
        Student::factory()->create([
            'user_id' => $adminUser->id,
            'name' => $adminUser->name,
            'student_id' => 'ADMIN001',
        ]);
    }
}
