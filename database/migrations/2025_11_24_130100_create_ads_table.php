<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('start_date');
            $table->dateTime('expiry_date');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('tenant_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ads');
    }
};