<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Services\VideoThumbnailService;

class VideoGenerateThumbnails extends Command
{
    protected $signature = 'video:generate-thumbnails {--all : Generate for all videos} {--force : Force regenerate}';
    protected $description = 'Generate thumbnails for videos';
    
    public function handle()
    {
        if ($this->option('all')) {
            $videos = Video::all();
        } else {
            $videos = Video::whereNull('thumbnail')
            ->orWhere('thumbnail', '')
            ->orWhere('thumbnail', 'like', 'color_thumb%') // Перегенерируем цветные превью
            ->get();
        }
        
        if ($this->option('force')) {
            $videos = Video::all();
        }
        
        $this->info("Найдено видео для обработки: " . $videos->count());
        
        if ($videos->count() === 0) {
            $this->info("Нет видео для обработки.");
            return;
        }
        
        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        
        $successVideo = 0;
        $successColor = 0;
        $errors = 0;
        
        foreach ($videos as $video) {
            try {
                // Удаляем старое превью если force
                if ($this->option('force') && $video->thumbnail) {
                    $oldPath = storage_path('app/public/' . $video->thumbnail);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                    $video->thumbnail = null;
                    $video->save();
                }
                
                // Проверяем существует ли видео файл
                $videoPath = storage_path('app/public/videos/' . $video->filename);
                
                if (file_exists($videoPath)) {
                    // Пытаемся создать превью из видео (10 секунда)
                    if (VideoThumbnailService::generateThumbnail($video, 10)) {
                        $successVideo++;
                    }
                } else {
                    $this->warn("\n⚠ Видеофайл не найден: {$video->filename}");
                    // Создаем цветное превью
                    if (VideoThumbnailService::createColorfulThumbnail($video)) {
                        $successColor++;
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("\n✗ Ошибка для '{$video->title}': " . $e->getMessage());
                $errors++;
            }
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("✅ Генерация превью завершена!");
        $this->info("✓ Создано из видео: {$successVideo}");
        $this->info("✓ Создано цветных: {$successColor}");
        $this->info("✗ Ошибок: {$errors}");
        
        if ($errors > 0) {
            $this->warn("Некоторые превью не были сгенерированы.");
        }
    }
}