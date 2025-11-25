<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->unsignedBigInteger('reel_group_id')->nullable()->after('id');
            $table->integer('sort_order')->default(0)->after('reel_group_id');
            $table->foreign('reel_group_id')
                ->references('id')
                ->on('reel_groups')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->dropForeign(['reel_group_id']);
            $table->dropColumn('reel_group_id');
            $table->dropColumn('sort_order');
        });
    }
};
