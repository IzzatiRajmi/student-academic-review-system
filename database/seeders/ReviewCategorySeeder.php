<?php

namespace Database\Seeders;

use App\Models\ReviewCategory;
use Illuminate\Database\Seeder;

class ReviewCategorySeeder extends Seeder
{
    /** Same categories as the dev branch. */
    public function run(): void
    {
        foreach ([
            'Semakan Markah Peperiksaan',
            'Semakan Markah Kerja Kursus',
            'Semakan Gred Akhir',
            'Keputusan Pentadbiran Akademik',
        ] as $name) {
            ReviewCategory::firstOrCreate(['name' => $name]);
        }
    }
}
