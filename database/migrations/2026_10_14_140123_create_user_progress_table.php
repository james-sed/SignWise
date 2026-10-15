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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->integer('quiz_score')->default(0);
            $table->integer('response_time_ms'); // Latency in milliseconds between performing gesture and receiving feedback[cite: 10, 28]
            $table->decimal('match_accuracy', 5, 2); // Calculated percentage match score from MediaPipe detection trial[cite: 10, 28]
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    } // 'user_id', 'lesson_id', 'quiz_score', 'response_time_ms', 'match_accuracy', 'completed'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
