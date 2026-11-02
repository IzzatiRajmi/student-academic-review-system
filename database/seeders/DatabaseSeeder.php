<?php

namespace Database\Seeders;

use App\Models\ReviewCategory;
use App\Models\ReviewRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Sample data that matches the wireframe numbers:
     * admin sees 10 / 3 / 6 / 1, Syamimi sees 3 / 1 / 2 / 0.
     */
    public function run(): void
    {
        $this->call(ReviewCategorySeeder::class);
        $cat = ReviewCategory::pluck('id', 'name');
        $exam = $cat['Semakan Markah Peperiksaan'];
        $course = $cat['Semakan Markah Kerja Kursus'];
        $grade = $cat['Semakan Gred Akhir'];
        $admin_ = $cat['Keputusan Pentadbiran Akademik'];

        $admin = User::create(['name' => 'Admin Fakulti', 'email' => 'admin@sars.test', 'password' => 'Admin@123', 'role' => 'admin']);

        $students = collect([
            ['syamimi', 'Syamimi binti Nadzri'],
            ['aminah', 'Aminah Najihah binti Basir'],
            ['amani', 'Amani Nur Aisyah binti Rosli'],
            ['nurul', 'Nurul Izzah binti Kamal'],
        ])->mapWithKeys(fn ($s) => [$s[0] => User::create([
            'name' => $s[1], 'email' => "{$s[0]}@siswa.upsi.edu.my", 'password' => 'Student@123', 'role' => 'student',
        ])]);

        // student, category, subject, justification, status, submitted, processed, remarks
        $rows = [
            ['syamimi', $exam, 'KPF3012', 'Request to review my final exam grade. I believe the marks for Section B were not fully counted based on the answer scheme.', 'Pending', '2026-10-26 09:15', null, null],
            ['syamimi', $course, 'DES3073', 'My group project marks are missing the presentation component. Please review the coursework total.', 'Approved', '2026-10-23 10:02', '2026-10-25 14:30', 'Your request has been approved. Please check your updated result in the system.'],
            ['syamimi', $course, 'MTE3013', 'Quiz 2 marks were recorded as zero although I attended and submitted the quiz.', 'Approved', '2026-10-23 11:40', '2026-10-24 09:00', 'Marks updated after checking the attendance record.'],
            ['aminah', $exam, 'KPF3022', 'I would like my final exam script re-marked as the total does not match the marks shown on each page.', 'Pending', '2026-10-27 08:30', null, null],
            ['aminah', $admin_, 'HBT1013', 'Assignment 1 was submitted on time but marked as late. Submission receipt attached to email.', 'Approved', '2026-10-15 16:20', '2026-10-18 10:10', 'Late penalty removed.'],
            ['aminah', $course, 'DES3063', 'Requesting a review of my lab test result because I felt the questions were unclear.', 'Rejected', '2026-10-12 13:05', '2026-10-14 15:45', 'Lab test marking was checked and is correct. No change to the result.'],
            ['amani', $grade, 'HBP2033', 'My carry mark is lower than my own calculation. Please review the coursework breakdown and final grade.', 'Pending', '2026-10-28 09:50', null, null],
            ['amani', $exam, 'DES3043', 'Mid-semester test marks for question 4 seem to be missing from the total.', 'Approved', '2026-10-10 10:00', '2026-10-13 11:20', 'Question 4 marks added.'],
            ['amani', $grade, 'KPF2012', 'Requesting review of my practicum evaluation score as one assessor form was not included.', 'Approved', '2026-10-08 14:35', '2026-10-11 09:30', 'Second assessor form included. Result updated.'],
            ['nurul', $course, 'DES2013', 'Final project demo marks were not recorded in the system.', 'Approved', '2026-10-05 15:10', '2026-10-07 10:40', 'Demo marks recorded.'],
        ];

        foreach ($rows as [$student, $categoryId, $code, $text, $status, $created, $processed, $remarks]) {
            $r = new ReviewRequest(['review_category_id' => $categoryId, 'subject_code' => $code, 'justification' => $text]);
            $r->user_id = $students[$student]->id;
            $r->status = $status;
            $r->admin_remarks = $remarks;
            $r->processed_by = $processed ? $admin->id : null;
            $r->processed_at = $processed ? Carbon::parse($processed) : null;
            $r->created_at = Carbon::parse($created);
            $r->updated_at = Carbon::parse($processed ?? $created);
            $r->save();
        }
    }
}
