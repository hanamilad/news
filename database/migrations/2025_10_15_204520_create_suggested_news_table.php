<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suggested_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('suggested_news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('suggested_news');
    }
};
