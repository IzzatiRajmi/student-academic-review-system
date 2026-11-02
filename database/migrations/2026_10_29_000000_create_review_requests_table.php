<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REVIEW_REQUESTS (ERD): review_request_id (id), user_id, review_category_id, subject_code,
     * justification, status, processed_by, admin_remarks, processed_at, created_at, updated_at
     */
    public function up(): void
    {
        Schema::create('review_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();          // submits
            $table->foreignId('review_category_id')->constrained('review_categories');      // classifies
            $table->string('subject_code');
            $table->text('justification');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->index();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // processes
            $table->text('admin_remarks')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_requests');
    }
};
