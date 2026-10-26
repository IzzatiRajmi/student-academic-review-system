<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void {
    User::forceCreate([
        'name' => 'Admin Fakulti',
        'email' => 'admin@sars.test',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    User::forceCreate([
        'name' => 'Student One',
        'email' => 'student1@siswa.upsi.edu.my',
        'password' => bcrypt('password'),
        'role' => 'student',
    ]);

    User::forceCreate([
        'name' => 'Student Two',
        'email' => 'student2@siswa.upsi.edu.my',
        'password' => bcrypt('password'),
        'role' => 'student',
    ]);

    $this->call(ReviewCategorySeeder::class);
    }
}
