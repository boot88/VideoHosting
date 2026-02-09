<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class GenerateVideoThumbnails extends Command
{
    protected $signature = 'video:thumbnails';
    
    public function handle()
    {
        $videos = Video::whereNull('thumbnail')->get();
        
        foreach ($videos as $video) {
            $videoPath = storage_path('app/public/videos/' . $video->filename);
            $thumbnailPath = 'thumbnails/' . pathinfo($video->filename, PATHINFO_FILENAME) . '.jpg';
            
            if (file_exists($videoPath)) {
                try {
                    $ffmpeg = FFMpeg::create();
                    $videoFile = $ffmpeg->open($videoPath);
                    
                    // Берем кадр на 15 секунде
                    $videoFile
                        ->frame(TimeCode::fromSeconds(15))
                        ->save(storage_path('app/public/' . $thumbnailPath));
                    
                    $video->thumbnail = $thumbnailPath;
                    $video->save();
                    
                    $this->info("Thumbnail created for: {$video->title}");
                } catch (\Exception $e) {
                    $this->error("Error for {$video->title}: " . $e->getMessage());
                }
            }
        }
    }
}