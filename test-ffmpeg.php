<?php
// test-thumbnail.php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Log;
use App\Models\Video;

echo "Testing thumbnail generation...\n\n";

// Берем первое видео
$video = Video::first();

if (!$video) {
    die("No videos found in database!\n");
}

echo "Video ID: {$video->id}\n";
echo "Title: {$video->title}\n";
echo "Filename: {$video->filename}\n";
echo "Current thumbnail: {$video->thumbnail}\n";

$videoPath = storage_path('app/public/videos/' . $video->filename);
echo "\nVideo path: {$videoPath}\n";
echo "Video exists: " . (file_exists($videoPath) ? "✅ Yes" : "❌ No") . "\n";

if (!file_exists($videoPath)) {
    die("Video file not found!\n");
}

// Путь к ffmpeg
$ffmpegPath = 'C:\Soz\laragon\bin\ffmpeg\bin\ffmpeg.exe';
echo "\nFFMPEG path: {$ffmpegPath}\n";
echo "FFMPEG exists: " . (file_exists($ffmpegPath) ? "✅ Yes" : "❌ No") . "\n";

// Тестовый путь для превью
$testThumbnail = storage_path('app/public/thumbnails/debug_test.jpg');
$thumbDir = dirname($testThumbnail);

echo "\nThumbnail dir: {$thumbDir}\n";
echo "Thumbnail dir exists: " . (file_exists($thumbDir) ? "✅ Yes" : "❌ No") . "\n";
echo "Thumbnail dir writable: " . (is_writable($thumbDir) ? "✅ Yes" : "❌ No") . "\n";

if (!file_exists($thumbDir)) {
    mkdir($thumbDir, 0755, true);
    echo "Created thumbnail directory\n";
}

// Команда для теста
$videoPath = str_replace('/', '\\', $videoPath);
$testThumbnail = str_replace('/', '\\', $testThumbnail);

$command = "\"{$ffmpegPath}\" -i \"{$videoPath}\" -ss 00:00:10 -vframes 1 -q:v 2 \"{$testThumbnail}\" 2>&1";

echo "\nCommand:\n{$command}\n\n";

echo "Executing command...\n";
$output = shell_exec($command);
echo "Output:\n{$output}\n";

if (file_exists($testThumbnail)) {
    $size = filesize($testThumbnail);
    echo "\n✅ SUCCESS! Thumbnail created: {$testThumbnail}\n";
    echo "Size: " . round($size/1024, 2) . " KB\n";
    
    // Проверяем тип файла
    $imageInfo = @getimagesize($testThumbnail);
    if ($imageInfo) {
        echo "Image type: " . image_type_to_mime_type($imageInfo[2]) . "\n";
        echo "Dimensions: {$imageInfo[0]} x {$imageInfo[1]}\n";
        
        // Показываем первые байты
        $handle = fopen($testThumbnail, 'rb');
        $bytes = fread($handle, 20);
        fclose($handle);
        echo "First 20 bytes (hex): " . bin2hex($bytes) . "\n";
        
        // JPEG должен начинаться с FF D8 FF
        if (bin2hex(substr($bytes, 0, 3)) === 'ffd8ff') {
            echo "✅ Valid JPEG signature (FF D8 FF)\n";
        } else {
            echo "❌ Invalid JPEG signature\n";
        }
    } else {
        echo "❌ Not a valid image file\n";
    }
} else {
    echo "\n❌ FAILED! Thumbnail not created.\n";
    
    // Проверяем ошибки
    if (strpos($output, 'Invalid data found') !== false) {
        echo "Error: Invalid video file format\n";
    } elseif (strpos($output, 'No such file or directory') !== false) {
        echo "Error: File not found\n";
    } elseif (strpos($output, 'Permission denied') !== false) {
        echo "Error: Permission denied\n";
    }
}