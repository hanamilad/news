<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('tenant_id');
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
