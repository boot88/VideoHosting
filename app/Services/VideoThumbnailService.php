<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\Video\VideoFilters;

class VideoThumbnailService
{
    public static function generateThumbnail(Video $video, $second = 10)
    {
        try {
            // Пытаемся создать превью из видео
            return self::createVideoThumbnail($video, $second);
            
        } catch (\Exception $e) {
            Log::error('Video thumbnail generation failed: ' . $e->getMessage());
            
            // Если не получилось из видео, создаем цветное превью
            return self::createColorfulThumbnail($video);
        }
    }
    
    private static function createVideoThumbnail(Video $video, $second = 10)
    {
        try {
            $videoPath = storage_path('app/public/videos/' . $video->filename);
            
            // Проверяем существование файла
            if (!file_exists($videoPath)) {
                Log::error("Video file not found: " . $videoPath);
                throw new \Exception("Video file not found: " . $video->filename);
            }
            
            Log::info("Processing video: {$video->title}, path: {$videoPath}");
            
            // Создаем имя для превью
            $thumbnailName = 'video_thumb_' . $video->id . '.jpg';
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailName);
            
            // Создаем папку если не существует
            $thumbnailsDir = dirname($thumbnailPath);
            if (!file_exists($thumbnailsDir)) {
                mkdir($thumbnailsDir, 0755, true);
            }
            
            // Получаем путь к ffmpeg из конфига
            $ffmpegPath = config('ffmpeg.ffmpeg_path', 'C:\Soz\laragon\bin\ffmpeg\bin\ffmpeg.exe');
            
            // Для Windows: используем двойные кавычки и нормализуем пути
            $videoPath = str_replace('/', '\\', $videoPath);
            $thumbnailPath = str_replace('/', '\\', $thumbnailPath);
            $ffmpegPath = str_replace('/', '\\', $ffmpegPath);
            
            // Простая команда ffmpeg для создания скриншота
            // -ss 10: перейти на 10 секунду
            // -vframes 1: взять 1 кадр
            // -q:v 2: качество 2 (лучшее качество)
            $command = "\"{$ffmpegPath}\" -i \"{$videoPath}\" -ss 00:00:10 -vframes 1 -q:v 2 \"{$thumbnailPath}\" 2>&1";
            
            Log::info("Executing FFMPEG command: " . $command);
            
            // Выполняем команду
            $output = shell_exec($command);
            Log::info("FFMPEG output: " . $output);
            
            // Проверяем создался ли файл
            if (file_exists($thumbnailPath) && filesize($thumbnailPath) > 0) {
                $size = filesize($thumbnailPath);
                Log::info("✅ Thumbnail created: {$thumbnailPath}, size: {$size} bytes");
                
                // Проверяем, что это действительно JPEG
                $imageInfo = @getimagesize($thumbnailPath);
                if ($imageInfo && $imageInfo[2] === IMAGETYPE_JPEG) {
                    Log::info("✅ Valid JPEG image: " . print_r($imageInfo, true));
                    
                    // Оптимизируем изображение
                    self::optimizeThumbnail($thumbnailPath);
                    
                    // Обновляем видео
                    $video->thumbnail = 'thumbnails/' . $thumbnailName;
                    $video->save();
                    
                    Log::info("✅ Video thumbnail saved for: {$video->title}");
                    return true;
                } else {
                    Log::error("❌ Created file is not a valid JPEG");
                    @unlink($thumbnailPath); // Удаляем некорректный файл
                    throw new \Exception("Created file is not a valid JPEG");
                }
            } else {
                Log::error("❌ Thumbnail was not created or is empty: {$thumbnailPath}");
                Log::error("FFMPEG output was: " . $output);
                throw new \Exception("Thumbnail file was not created. FFMPEG output: " . $output);
            }
            
        } catch (\Exception $e) {
            Log::error('Video thumbnail creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private static function optimizeThumbnail($thumbnailPath)
    {
        try {
            // Используем GD для оптимизации
            if (function_exists('imagecreatefromjpeg')) {
                $image = imagecreatefromjpeg($thumbnailPath);
                if ($image) {
                    // Уменьшаем размер до 1280x720 если больше
                    $width = imagesx($image);
                    $height = imagesy($image);
                    
                    if ($width > 1280 || $height > 720) {
                        $newWidth = 1280;
                        $newHeight = 720;
                        
                        $newImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($newImage, $image, 0, 0, 0, 0,
                            $newWidth, $newHeight, $width, $height);
                        
                        imagejpeg($newImage, $thumbnailPath, 85); // Качество 85%
                        imagedestroy($newImage);
                    } else {
                        imagejpeg($image, $thumbnailPath, 85);
                    }
                    
                    imagedestroy($image);
                }
            }
        } catch (\Exception $e) {
            Log::error('Thumbnail optimization failed: ' . $e->getMessage());
        }
    }
    
    private static function createColorfulThumbnail(Video $video)
    {
        // Оставляем существующий метод как fallback
        try {
            $thumbnailName = 'color_thumb_' . md5($video->title . $video->id) . '.jpg';
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailName);
            
            // Создаем папку если не существует
            $thumbnailsDir = dirname($thumbnailPath);
            if (!file_exists($thumbnailsDir)) {
                mkdir($thumbnailsDir, 0755, true);
            }
            
            // Массив красивых градиентов
            $gradients = [
                ['#ff6b9d', '#4ecdc4'],
                ['#667eea', '#764ba2'],
                ['#f093fb', '#f5576c'],
                ['#4facfe', '#00f2fe'],
                ['#43e97b', '#38f9d7'],
            ];
            
            $gradientIndex = ($video->id ?? rand(0, 9)) % count($gradients);
            $gradient = $gradients[$gradientIndex];
            
            $width = 1280;
            $height = 720;
            
            if (!function_exists('imagecreatetruecolor')) {
                throw new \Exception('GD extension not available');
            }
            
            $image = imagecreatetruecolor($width, $height);
            
            // Создаем градиент
            $color1 = self::hexToRgb($gradient[0]);
            $color2 = self::hexToRgb($gradient[1]);
            
            for($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int)($ratio * $color2['r'] + (1 - $ratio) * $color1['r']);
                $g = (int)($ratio * $color2['g'] + (1 - $ratio) * $color1['g']);
                $b = (int)($ratio * $color2['b'] + (1 - $ratio) * $color1['b']);
                
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $y, $width, $y, $color);
            }
            
            // Сохраняем изображение
            imagejpeg($image, $thumbnailPath, 90);
            imagedestroy($image);
            
            // Обновляем видео
            $video->thumbnail = 'thumbnails/' . $thumbnailName;
            $video->save();
            
            Log::info("Colorful thumbnail created for: {$video->title}");
            return true;
            
        } catch (\Exception $e) {
            Log::error('Color thumbnail creation failed: ' . $e->getMessage());
            return self::createSimpleThumbnail($video);
        }
    }
    
    private static function createSimpleThumbnail(Video $video)
    {
        try {
            $thumbnailName = 'simple_thumb_' . md5($video->title . $video->id) . '.jpg';
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailName);
            
            // Создаем папку если не существует
            $thumbnailsDir = dirname($thumbnailPath);
            if (!file_exists($thumbnailsDir)) {
                mkdir($thumbnailsDir, 0755, true);
            }
            
            $width = 1280;
            $height = 720;
            
            $image = imagecreatetruecolor($width, $height);
            
            // Простой однотонный фон
            $bgColor = imagecolorallocate($image, 40, 40, 60);
            imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
            
            // Текст
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $title = substr($video->title, 0, 30);
            
            // Центрируем текст
            $textX = ($width - (strlen($title) * 10)) / 2;
            $textY = $height / 2 - 20;
            
            imagestring($image, 5, $textX, $textY, $title, $textColor);
            imagestring($image, 3, $textX, $textY + 30, 'Нажмите для просмотра', $textColor);
            
            imagejpeg($image, $thumbnailPath, 80);
            imagedestroy($image);
            
            $video->thumbnail = 'thumbnails/' . $thumbnailName;
            $video->save();
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Simple thumbnail also failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private static function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return ['r' => $r, 'g' => $g, 'b' => $b];
    }
    
    private static function getFontPath()
    {
        $possiblePaths = [
            resource_path('fonts/arial.ttf'),
            resource_path('fonts/Roboto-Regular.ttf'),
            storage_path('fonts/arial.ttf'),
            'C:\\Windows\\Fonts\\arial.ttf',
            'C:\\Windows\\Fonts\\calibri.ttf',
            'C:\\Windows\\Fonts\\tahoma.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    public static function getThumbnailUrl($thumbnailPath)
    {
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }
        
        // Создаем SVG плейсхолдер на лету
        $videoId = rand(1, 50);
        $colors = [
            ['#ff6b9d', '#4ecdc4'],
            ['#667eea', '#764ba2'],
            ['#f093fb', '#f5576c'],
            ['#4facfe', '#00f2fe'],
            ['#43e97b', '#38f9d7'],
        ];
        
        $color = $colors[$videoId % count($colors)];
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720">
            <defs>
                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="' . $color[0] . '"/>
                    <stop offset="100%" stop-color="' . $color[1] . '"/>
                </linearGradient>
            </defs>
            <rect width="1280" height="720" fill="url(#grad)"/>
            <circle cx="640" cy="320" r="80" fill="rgba(255,255,255,0.2)"/>
            <circle cx="640" cy="320" r="60" fill="' . $color[0] . '"/>
            <polygon points="610,290 690,320 610,350" fill="white"/>
            <text x="640" y="480" text-anchor="middle" fill="white" font-family="Arial" font-size="36" font-weight="bold">FASHION VIDEO</text>
            <text x="640" y="530" text-anchor="middle" fill="rgba(255,255,255,0.9)" font-family="Arial" font-size="24">Video #' . $videoId . '</text>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}