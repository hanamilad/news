<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hashtag_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained('hashtags')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hashtag_news');
    }
};
