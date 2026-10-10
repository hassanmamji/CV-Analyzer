<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path');
            $table->longText('extracted_text');
            $table->string('job_role')->nullable();
            $table->unsignedTinyInteger('score')->default(0);
            $table->json('result')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_analyses');
    }
};
