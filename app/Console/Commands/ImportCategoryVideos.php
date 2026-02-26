<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\VideoThumbnailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCategoryVideos extends Command
{
    protected $signature = 'video:import-categories
                            {source=public/images/import : Путь к папке с категориями}
                            {--compression=light : none|light|strong}
                            {--thumbnails : Генерировать превью для новых видео}
                            {--dry-run : Только показать, что будет сделано}';

    protected $description = 'Импортирует видео из подпапок (категорий), добавляет только новые, опционально сжимает и делает превью';

    public function handle(): int
    {
        $sourceDir = base_path($this->argument('source'));
        $compression = strtolower((string) $this->option('compression'));
        $dryRun = (bool) $this->option('dry-run');
        $withThumbs = (bool) $this->option('thumbnails');

        if (!in_array($compression, ['none', 'light', 'strong'], true)) {
            $this->error('Опция --compression должна быть: none|light|strong');
            return self::FAILURE;
        }

        if (!is_dir($sourceDir)) {
            $this->error("Папка не найдена: {$sourceDir}");
            return self::FAILURE;
        }

        $this->info("Сканирую папку: {$sourceDir}");

        $videoFiles = $this->discoverVideos($sourceDir);

        if (empty($videoFiles)) {
            $this->warn('Видео не найдены. Ожидается структура вида: import/<category>/*.mp4');
            return self::SUCCESS;
        }

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($videoFiles as $filePath) {
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);
            $sourcePathForDb = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            if (Video::where('source_path', $sourcePathForDb)->exists()) {
                $skipped++;
                $this->line("SKIP (уже импортировано): {$sourcePathForDb}");
                continue;
            }

            $categoryName = basename(dirname($filePath));
            $category = Str::slug($categoryName);

            $baseName = pathinfo($filePath, PATHINFO_FILENAME);
            $hash = substr(sha1($sourcePathForDb), 0, 10);
            $targetFilename = Str::slug($categoryName . '-' . $baseName) . '-' . $hash . '.mp4';

            $targetStorageRelative = 'videos/' . $targetFilename;
            $targetAbs = storage_path('app/public/' . $targetStorageRelative);

            if (!$dryRun) {
                if (!is_dir(dirname($targetAbs))) {
                    mkdir(dirname($targetAbs), 0755, true);
                }

                $ok = $this->processVideo($filePath, $targetAbs, $compression);
                if (!$ok) {
                    $errors++;
                    $this->error("FAIL: {$sourcePathForDb}");
                    continue;
                }

                $duration = $this->probeDuration($targetAbs);

                $video = Video::create([
                    'title' => Str::title(str_replace(['-', '_'], ' ', $baseName)),
                    'slug' => Str::slug($categoryName . '-' . $baseName . '-' . $hash),
                    'description' => 'Автоимпорт из категории: ' . $categoryName,
                    'filename' => $targetFilename,
                    'thumbnail' => null,
                    'duration' => $duration,
                    'format' => 'MP4',
                    'quality' => $compression === 'none' ? 'Source' : ($compression === 'light' ? 'Optimized' : 'Compressed'),
                    'views' => 0,
                    'likes' => 0,
                    'featured' => false,
                    'category' => $category,
                    'source_path' => $sourcePathForDb,
                    'source_mtime' => (int) @filemtime($filePath),
                ]);

                if ($withThumbs) {
                    VideoThumbnailService::generateThumbnail($video, 10);
                }
            }

            $imported++;
            $this->info("OK: {$sourcePathForDb} -> {$targetStorageRelative}");
        }

        $this->newLine();
        $this->info("Готово. Добавлено: {$imported}, пропущено: {$skipped}, ошибок: {$errors}");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function discoverVideos(string $sourceDir): array
    {
        $extensions = ['mp4', 'mov', 'mkv', 'avi', 'webm'];
        $files = [];

        $categoryDirs = glob($sourceDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];

        foreach ($categoryDirs as $categoryDir) {
            foreach ($extensions as $ext) {
                $matches = glob($categoryDir . DIRECTORY_SEPARATOR . '*.' . $ext) ?: [];
                $matchesUpper = glob($categoryDir . DIRECTORY_SEPARATOR . '*.' . strtoupper($ext)) ?: [];
                $files = array_merge($files, $matches, $matchesUpper);
            }
        }

        return array_values(array_unique($files));
    }

    private function processVideo(string $sourceAbs, string $targetAbs, string $compression): bool
    {
        if ($compression === 'none') {
            return copy($sourceAbs, $targetAbs);
        }

        $ffmpegPath = config('ffmpeg.ffmpeg_path', 'ffmpeg');

        $vf = $compression === 'strong' ? '-vf "scale=min(1280\\,iw):-2"' : '';
        $crf = $compression === 'strong' ? '32' : '27';
        $audio = $compression === 'strong' ? '96k' : '128k';

        $command = sprintf(
            '"%s" -y -i "%s" %s -c:v libx264 -preset veryfast -crf %s -c:a aac -b:a %s "%s" 2>&1',
            $ffmpegPath,
            $sourceAbs,
            $vf,
            $crf,
            $audio,
            $targetAbs
        );

        exec($command, $output, $code);

        return $code === 0 && file_exists($targetAbs) && filesize($targetAbs) > 0;
    }

    private function probeDuration(string $videoAbsPath): int
    {
        $ffprobePath = config('ffmpeg.ffprobe_path', 'ffprobe');

        $command = sprintf(
            '"%s" -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "%s" 2>&1',
            $ffprobePath,
            $videoAbsPath
        );

        $result = trim((string) shell_exec($command));

        return is_numeric($result) ? (int) round((float) $result) : 0;
    }
}
