<?php

namespace Database\Seeders;

use App\Models\ReviewCategory;
use Illuminate\Database\Seeder;

class ReviewCategorySeeder extends Seeder
{
    /**
     * Seed the review categories.
     */
    public function run(): void
    {
        $categories = [
            'Semakan Markah Peperiksaan',
            'Semakan Markah Kerja Kursus',
            'Semakan Gred Akhir',
            'Keputusan Pentadbiran Akademik',
        ];

        foreach ($categories as $name) {
            ReviewCategory::firstOrCreate(['name' => $name]);
        }
    }
}
