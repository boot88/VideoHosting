<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 65; // видео на странице (пирамида: 1+2+3+4+5*11 = 65)
        $page = $request->get('page', 1);
        
        // Получаем видео с подсчетом комментариев и сортируем по рейтингу (просмотры + комментарии)
        $videos = Video::withCount('comments')
            ->orderByRaw('(views + comments_count + likes) DESC')
            ->orderBy('views', 'DESC')
            ->orderBy('comments_count', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Группируем видео по "этажам" для веб-версии
        $groupedVideos = $this->groupVideosForWeb($videos->items());
        
        return view('video.index', compact('videos', 'groupedVideos'));
    }
    
    private function groupVideosForWeb($videos)
    {
        $groups = [];
        $index = 0;
        $floorSizes = [1, 2, 3, 4]; // первые 4 этажа
        
        // Первые 4 этажа (1, 2, 3, 4 видео)
        foreach ($floorSizes as $size) {
            if ($index >= count($videos)) break;
            $groups[] = array_slice($videos, $index, $size);
            $index += $size;
        }
        
        // Остальные этажи по 5 видео
        while ($index < count($videos)) {
            $groups[] = array_slice($videos, $index, 5);
            $index += 5;
        }
        
        return $groups;
    }
    
    public function show($slug)
    {
        $video = Video::where('slug', $slug)
            ->withCount('comments')
            ->firstOrFail();
        
        // Увеличиваем просмотры и обновляем рейтинг
        $video->increment('views');
        $video->rating = $video->views + $video->comments_count + $video->likes;
        $video->save();
        
        $comments = $video->comments()->latest()->get();
        
        return view('video.show', compact('video', 'comments'));
    }
    
    public function storeComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'username' => 'nullable|string|max:50'
        ]);
        
        $video = Video::where('slug', $slug)->firstOrFail();
        
        // Проверка защиты от спама
        $lastComment = Comment::where('ip_address', $request->ip())
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastComment && $lastComment->created_at->diffInMinutes(now()) < 1) {
            return back()->withErrors(['content' => 'Пожалуйста, подождите минуту перед отправкой следующего комментария']);
        }
        
        $comment = $video->comments()->create([
            'username' => $request->username ?: 'Аноним',
            'content' => strip_tags($request->content),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);
        
        // Обновляем рейтинг видео
        $video->refresh(); // Обновляем данные, чтобы получить актуальное количество комментариев
        $video->rating = $video->views + $video->comments()->count() + $video->likes;
        $video->save();
        
        return back()->with('success', 'Комментарий добавлен!');
    }
    
    public function fullscreen($slug)
    {
        $video = Video::where('slug', $slug)->firstOrFail();
        return view('video.fullscreen', compact('video'));
    }
    
    // Метод для обновления рейтингов всех видео (можно запускать по расписанию)
    public function updateAllRatings()
    {
        $videos = Video::withCount('comments')->get();
        
        foreach ($videos as $video) {
            $video->rating = $video->views + $video->comments_count + $video->likes;
            $video->save();
        }
        
        return response()->json(['message' => 'Рейтинги обновлены', 'count' => $videos->count()]);
    }
}