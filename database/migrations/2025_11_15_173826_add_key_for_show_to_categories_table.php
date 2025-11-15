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
            $table->json('description')->after('name');
            $table->boolean('show_in_navbar')->default(false)->after('description');
            $table->boolean('show_in_homepage')->default(false)->after('show_in_navbar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('show_in_navbar');
            $table->dropColumn('show_in_homepage');
        });
    }
};
