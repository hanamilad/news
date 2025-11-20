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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_in_grid')->default(false)->after('show_in_homepage');
            $table->unsignedInteger('grid_order')->nullable()->after('show_in_grid');
            $table->unique(['tenant_id', 'grid_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'grid_order']);
            $table->dropColumn(['show_in_grid', 'grid_order']);
        });
    }
};
