<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Services\VideoThumbnailService;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    public function run()
    {
        // Очищаем таблицу
        DB::table('videos')->truncate();
        
        echo "Создание 160 видео (без копирования файлов)...\n";
        
        // Массив случайных описаний
        $descriptions = [
            "Захватывающие моменты из повседневной жизни",
            "Самые смешные и невероятные ситуации",
            "То, что заставит вас улыбнуться",
            "Невероятные совпадения и курьёзные случаи",
            "Лучшие моменты, собранные в одном видео",
            "То, что вы не увидите больше нигде",
            "Эмоции, которые трудно передать словами",
            "Уникальные кадры из разных уголков мира",
            "Моменты, которые вошли в историю",
            "То, о чем говорят все вокруг"
        ];
        
        // Массив качеств
        $qualities = ['HD', 'Full HD', '4K', '1080p', '720p'];
        $formats = ['MP4', 'AVI', 'MKV', 'MOV'];
        
        $categories = ['runway','streetwear','haute-couture','accessories','beauty','lookbook','backstage','editorial','menswear','sustainable','kids-fashion'];
 $sourceFiles = ['fashion_source_1.mp4','fashion_source_2.mp4','fashion_source_3.mp4','fashion_source_4.mp4'];

 for ($i = 1; $i <= 160; $i++) {
            $video = Video::create([
                'title' => "Fashion Collection Video #$i",
                'slug' => "fashion-collection-video-$i",
                'description' => $descriptions[array_rand($descriptions)] . " - Часть $i",
                'filename' => $sourceFiles[($i - 1) % count($sourceFiles)],
                'thumbnail' => null,
                'duration' => rand(120, 600),
                'format' => $formats[array_rand($formats)],
                'quality' => $qualities[array_rand($qualities)],
                'views' => rand(100, 10000),
                'likes' => rand(50, 5000),
                'rating' => rand(500, 15000),
                'featured' => $i === 1,
 'category' => $categories[($i - 1) % count($categories)],
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ]);
            
            echo "Создано видео {$i}/160: {$video->title}\n";
        }
        
        echo "Все 160 видео созданы без дублирования файлов!\n";
        echo "Теперь запустите: php artisan video:generate-thumbnails --all\n";
    }
}