<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\NewsImage; 

class MigrateImagesToSpaces extends Command
{
    protected $signature = 'migrate:images-spaces';
    protected $description = 'نقل الصور من التخزين المحلي إلى DigitalOcean Spaces وتحديث قاعدة البيانات وحذف الملفات القديمة';

    public function handle()
    {
        $this->info("بدء عملية نقل الصور إلى DigitalOcean Spaces...");

        // مثال على جدول Categories، كرر لكل جدول عندك
        $news_images = NewsImage::all();

        foreach ($news_images as $news_image) {

            $column = 'image_path'; 
            $localPath = $news_image->$column; 
            if (!$localPath) {
                $this->warn("السجل ID {$news_image->id} لا يحتوي على صورة");
                continue;
            }
            if (!Storage::disk('public')->exists($localPath)) {
                $this->warn("ملف غير موجود على السيرفر: $localPath");
                continue;
            }

            try {
                $content = Storage::disk('public')->get($localPath);
                Storage::disk('spaces')->put($localPath, $content);
                $news_image->update([$column => $localPath]);
                Storage::disk('public')->delete($localPath);
                $this->info("تم نقل الملف بنجاح: $localPath");
            } catch (\Exception $e) {
                $this->error("حدث خطأ مع الملف $localPath: " . $e->getMessage());
            }
        }
        $this->info("تم الانتهاء من نقل كل الصور.");
    }
}
