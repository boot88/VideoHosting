<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'slug', 'description', 'filename', 'thumbnail',
        'duration', 'format', 'quality', 'views', 'likes', 'featured', 'rating'
    ];
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function incrementViews()
    {
        $this->views++;
        $this->rating = $this->views + $this->comments()->count();
        $this->save();
    }
    
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/default-thumbnail.jpg');
    }
    
    public function getVideoUrlAttribute()
    {
        return asset('storage/videos/' . $this->filename);
    }
    
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}