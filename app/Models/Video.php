<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\VideoThumbnailService;

class Video extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'filename', 'thumbnail',
        'duration', 'format', 'quality', 'views', 'likes', 'featured', 'category'
    ];

    protected $appends = ['comments_count', 'thumbnail_url', 'formatted_duration', 'total_rating'];
    
    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class);
    }
    
   
    
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }
    
    public function getTotalRatingAttribute()
    {
        return $this->views + $this->comments_count + $this->likes;
    }
    
    public function getThumbnailUrlAttribute()
    {
        return VideoThumbnailService::getThumbnailUrl($this->thumbnail);
    }

    public function getVideoUrlAttribute()
    {
        // Проверяем существует ли файл
        $path = storage_path('app/public/videos/' . $this->filename);
        if (!file_exists($path)) {
            // Создаем заглушку если файла нет
            return $this->createDummyVideoUrl();
        }
        return asset('storage/videos/' . $this->filename);
    }
    
    private function createDummyVideoUrl()
    {
        // Возвращаем тестовое видео
        return 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    
    // Метод для обновления рейтинга
    public function updateRating()
    {
        $this->rating = $this->views + $this->comments()->count() + $this->likes;
        $this->save();
    }
}