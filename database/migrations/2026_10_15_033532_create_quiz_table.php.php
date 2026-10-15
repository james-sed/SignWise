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
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); //lessons will be the based for quizzes
            $table->string('title');
            $table->string('question_text'); // Prompt shown to user (e.g., "Perform the sign for 'Letter A'")
            $table->string('target_sign'); // Expected gesture label to tell MediaPipe which landmark set to check against
            $table->integer('passing_score')->default(80);
            $table->timestamps();
        });
    } // 'lesson_Id', 'title', 'question_text', 'target_sign', 'passing_score'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
