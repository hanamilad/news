<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('host_name');
            $table->json('description')->nullable();
            $table->string('audio_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tenant_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
