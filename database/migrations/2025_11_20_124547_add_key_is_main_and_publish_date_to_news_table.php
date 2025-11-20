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
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('is_active');
            $table->datetime('publish_date')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('is_main');
            $table->dropColumn('publish_date');
        });
    }
};


// DELETE FROM `migrations` WHERE `migrations`.`id` = 32;
// DELETE FROM `migrations` WHERE `migrations`.`id` = 33;
// DELETE FROM `migrations` WHERE `migrations`.`id` = 34;
// DELETE FROM `migrations` WHERE `migrations`.`id` = 35;


// ALTER TABLE news DROP COLUMN is_main;
// ALTER TABLE news DROP COLUMN publish_date;
// ALTER TABLE articles DROP COLUMN publish_date;
// ALTER TABLE podcasts DROP COLUMN publish_date;
// ALTER TABLE videos DROP COLUMN publish_date;


// php artisan migrate --path=/database/migrations/2025_11_20_124547_add_key_is_main_and_publish_date_to_news_table.php
// php artisan migrate --path=/database/migrations/2025_11_20_134436_add_publish_date_to_articles_table.php
// php artisan migrate --path=/database/migrations/2025_11_20_134445_add_publish_date_to_podcasts_table.php
// php artisan migrate --path=/database/migrations/2025_11_20_134454_add_publish_date_to_videos_table.php