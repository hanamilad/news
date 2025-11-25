<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->boolean('is_main')->default(false);
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_images');
    }
};
