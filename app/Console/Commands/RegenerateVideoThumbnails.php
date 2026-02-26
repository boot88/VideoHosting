<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Services\VideoThumbnailService;
use Illuminate\Support\Facades\Storage;

class RegenerateVideoThumbnails extends Command
{
    protected $signature = 'video:regenerate-thumbnails 
                            {--all : Regenerate for all videos} 
                            {--skip=0 : Skip first N videos} 
                            {--limit=0 : Limit to N videos}
                            {--test : Test mode - only process 2 videos}';
    
    protected $description = 'Regenerate video thumbnails from actual video frames';
    
    public function handle()
    {
        $query = Video::query();
        
        if (!$this->option('all')) {
            // Только видео с цветными превью или без превью
            $query->where(function($q) {
                $q->whereNull('thumbnail')
                  ->orWhere('thumbnail', '')
                  ->orWhere('thumbnail', 'like', 'color_thumb%')
                  ->orWhere('thumbnail', 'like', 'simple_thumb%');
            });
        }
        
        if ($this->option('skip') > 0) {
            $query->skip($this->option('skip'));
        }
        
        if ($this->option('limit') > 0) {
            $query->take($this->option('limit'));
        }
        
        // Тестовый режим
        if ($this->option('test')) {
            $query->take(2);
        }
        
        $videos = $query->get();
        
        $this->info("Found {$videos->count()} videos to process");
        
        if ($videos->count() === 0) {
            $this->info("No videos to process.");
            return;
        }
        
        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        
        $success = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($videos as $video) {
            try {
                // Проверяем существует ли видео файл
                $videoPath = storage_path('app/public/videos/' . $video->filename);
                
                if (!file_exists($videoPath)) {
                    $this->warn("\n⚠ Video file not found: {$video->filename}");
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                $this->info("\nProcessing: {$video->title} (ID: {$video->id})");
                $this->info("Video file: {$video->filename}");
                
                // Удаляем старое превью если оно есть
                if ($video->thumbnail) {
                    $oldPath = storage_path('app/public/' . $video->thumbnail);
                    if (file_exists($oldPath) && !is_dir($oldPath)) {
                        try {
                            unlink($oldPath);
                            $this->info("Deleted old thumbnail: {$oldPath}");
                        } catch (\Exception $e) {
                            $this->warn("Could not delete old thumbnail: " . $e->getMessage());
                        }
                    }
                }
                
                // Генерируем новое превью с помощью обновленного сервиса
                $this->info("Generating thumbnail from 10th second...");
                
                // Используем прямое выполнение команды ffmpeg как в тесте
                $result = $this->generateThumbnailDirect($video, 10);
                
                if ($result) {
                    $success++;
                    $this->info("✅ Successfully generated for: {$video->title}");
                } else {
                    $errors++;
                    $this->error("❌ Failed for: {$video->title}");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\n✗ Error for '{$video->title}': " . $e->getMessage());
            }
            
            $bar->advance();
            
            // Небольшая пауза чтобы не перегружать систему
            if ($videos->count() > 10) {
                usleep(100000); // 0.1 секунда
            }
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("✅ Thumbnail regeneration completed!");
        $this->info("✓ Successfully regenerated: {$success}");
        $this->info("⚠ Skipped (no video file): {$skipped}");
        $this->info("✗ Errors: {$errors}");
        
        if ($errors > 0) {
            $this->warn("Some thumbnails failed to generate.");
        }
    }
    
    /**
     * Прямая генерация превью с помощью ffmpeg
     */
    private function generateThumbnailDirect(Video $video, $second = 10)
    {
        try {
            $videoPath = storage_path('app/public/videos/' . $video->filename);
            
            if (!file_exists($videoPath)) {
                throw new \Exception("Video file not found");
            }
            
            // Создаем имя для превью
            $thumbnailName = 'video_thumb_' . $video->id . '_' . time() . '.jpg';
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailName);
            
            // Создаем папку если не существует
            $thumbnailsDir = dirname($thumbnailPath);
            if (!file_exists($thumbnailsDir)) {
                mkdir($thumbnailsDir, 0755, true);
            }
            
            // Путь к ffmpeg (как в тесте)
            $ffmpegPath = 'C:\Soz\laragon\bin\ffmpeg\bin\ffmpeg.exe';
            
            // Экранируем пути для Windows
            $videoPath = str_replace('/', '\\', $videoPath);
            $thumbnailPath = str_replace('/', '\\', $thumbnailPath);
            
            // Команда для создания скриншота (работающая команда из теста)
            $command = "\"{$ffmpegPath}\" -i \"{$videoPath}\" -ss 00:00:{$second} -vframes 1 -q:v 2 \"{$thumbnailPath}\" 2>&1";
            
            $this->info("Executing: " . $command);
            
            // Выполняем команду
            $output = shell_exec($command);
            
            // Проверяем создался ли файл
            if (file_exists($thumbnailPath) && filesize($thumbnailPath) > 0) {
                $size = filesize($thumbnailPath);
                $this->info("Thumbnail created: {$thumbnailName} ({$size} bytes)");
                
                // Проверяем, что это действительно JPEG
                $imageInfo = @getimagesize($thumbnailPath);
                if ($imageInfo && $imageInfo[2] === IMAGETYPE_JPEG) {
                    // Обновляем видео
                    $video->thumbnail = 'thumbnails/' . $thumbnailName;
                    $video->save();
                    
                    $this->info("✅ Saved to database");
                    return true;
                } else {
                    @unlink($thumbnailPath); // Удаляем некорректный файл
                    throw new \Exception("Created file is not a valid JPEG");
                }
            } else {
                $this->error("FFMPEG output: " . $output);
                throw new \Exception("Thumbnail file was not created");
            }
            
        } catch (\Exception $e) {
            $this->error("Thumbnail generation failed: " . $e->getMessage());
            return false;
        }
    }
}