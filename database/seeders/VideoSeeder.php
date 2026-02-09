<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;

class VideoSeeder extends Seeder
{
    public function run()
    {
        $videos = [
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
            'funniest_home_videos_part_1',
            'funniest_home_videos_part_2',
            'funniest_home_videos_part_3',
        ];
        
        for ($i = 1; $i <= 50; $i++) {
            Video::create([
                'title' => "Funniest Home Videos Part $i",
                'slug' => "funniest-home-videos-part-$i",
                'description' => "Самое смешное домашнее видео часть $i. Невероятные моменты из жизни!",
                'filename' => "funniest_home_videos_part_$i.mp4",
                'duration' => rand(120, 600), // от 2 до 10 минут
                'format' => 'mp4',
                'quality' => ['SD', 'HD', 'FullHD'][rand(0, 2)],
                'views' => rand(0, 1000),
                'likes' => rand(0, 500),
                'rating' => rand(0, 1500),
                'featured' => $i === 1, // первое видео - featured
            ]);
        }
    }
}