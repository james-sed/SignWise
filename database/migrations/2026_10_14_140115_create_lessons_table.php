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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // string
            $table->string('sign_reference'); // string (stores gesture reference data/file path)
            $table->json('landmarks_data'); // Raw MediaPipe 21 landmark 3D coordinate array [x, y, z] used for real-time comparison[cite: 6, 11]
            $table->decimal('similarity_threshold', 3, 2)->default(0.85); // Minimum required similarity score (0.00 to 1.00) to confirm gesture match[cite: 6]
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard']); // diffciulty, this 3 data in the array is your only choices. nothing else.
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
