<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('review_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('review_category_id')->constrained('review_categories');
            $table->string('subject_code');
            $table->text('justification');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_remarks')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_requests');
    }
};
