<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->json('author_name')->nullable();
            $table->string('author_image')->nullable();
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
        Schema::dropIfExists('articles');
    }
};
