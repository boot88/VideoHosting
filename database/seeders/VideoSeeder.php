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
        
        echo "Создание 50 видео...\n";
        
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
        
        for ($i = 1; $i <= 50; $i++) {
            $video = Video::create([
                'title' => "Funniest Home Videos Part $i",
                'slug' => "funniest-home-videos-part-$i",
                'description' => $descriptions[array_rand($descriptions)] . " - Часть $i",
                'filename' => "funniest_home_videos_part_$i.mp4",
                'thumbnail' => null,
                'duration' => rand(120, 600),
                'format' => $formats[array_rand($formats)],
                'quality' => $qualities[array_rand($qualities)],
                'views' => rand(100, 10000),
                'likes' => rand(50, 5000),
                'rating' => rand(500, 15000),
                'featured' => $i === 1,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ]);
            
            echo "Создано видео {$i}/50: {$video->title}\n";
        }
        
        echo "Все 50 видео созданы!\n";
        echo "Теперь запустите: php artisan video:generate-thumbnails --all\n";
    }
}