<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('otp_hash');
            $table->timestamp('otp_expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->index(['phone', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
