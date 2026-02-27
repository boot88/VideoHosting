{{-- resources/views/video/index.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Video Hub | Модная видеоплатформа</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', -apple-system, system-ui, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            color: #fff;
            padding: 20px;
        }
        
        .fashion-container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(20, 20, 20, 0.9);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .fashion-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .fashion-header h1 {
            font-size: 2.8rem;
            font-weight: 300;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .fashion-header p {
            color: #aaa;
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .category-panel {
            margin: 0 auto 30px;
            max-width: 1100px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .category-chip {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.18);
            color: #ddd;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all .2s ease;
            background: rgba(255,255,255,0.03);
        }

        .category-chip:hover {
            border-color: rgba(255,107,157,0.55);
            color: #fff;
        }

        .category-chip.active {
            border-color: rgba(255,107,157,0.95);
            background: linear-gradient(45deg, rgba(255,107,157,.22), rgba(78,205,196,.22));
            color: #fff;
        }

        .empty-state {
            text-align: center;
            color: #bdbdbd;
            padding: 40px 20px;
            border: 1px dashed rgba(255,255,255,0.2);
            border-radius: 12px;
            margin: 20px 0 30px;
        }
        
        /* Featured Video */
        .featured-video {
            margin-bottom: 50px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            position: relative;
        }
        
        .featured-label {
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            color: white;
            padding: 10px 25px;
            display: inline-block;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 0 0 10px 10px;
            position: absolute;
            top: 0;
            left: 20px;
            z-index: 10;
        }
        
        /* Video Grid */
        .video-floor {
            margin-bottom: 40px;
        }
        
        .floor-title {
            font-size: 1.2rem;
            color: #aaa;
            margin-bottom: 20px;
            padding-left: 10px;
            border-left: 4px solid #ff6b9d;
        }
        
        /* Desktop Layout (Pyramid) */
        .video-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        /* Mobile Layout */
        .mobile-grid {
            display: none;
        }
        
        /* Video Card */
        .video-card {
            background: rgba(30, 30, 30, 0.7);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
            position: relative;
        }
        
        .video-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 107, 157, 0.3);
            box-shadow: 0 15px 30px rgba(255, 107, 157, 0.2);
        }
        
        .video-thumbnail {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 */
            background: #000;
            overflow: hidden;
        }
        
        .video-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .video-card:hover .video-thumbnail img {
            transform: scale(1.05);
        }
        
        .video-player {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }
        
        .video-card:hover .video-player {
            opacity: 1;
        }
        
        .video-player video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 3;
        }
        
        .video-card:hover .play-overlay {
            opacity: 1;
        }
        
        .play-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        }
        
        .video-info {
            padding: 20px;
        }
        
        .video-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px;
            color: #fff;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-meta {
            display: flex;
            justify-content: space-between;
            color: #aaa;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        
        .video-stats {
            display: flex;
            gap: 15px;
            color: #888;
            font-size: 0.8rem;
        }
        
        .stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .rating-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 4;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .rating-number {
            font-size: 1.1em;
        }
        
        .rating-score {
            font-size: 0.8em;
            opacity: 0.9;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 50px;
        }
        
        .page-link {
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .page-link:hover, .page-link.active {
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            color: white;
            border-color: transparent;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .fashion-container {
                padding: 15px;
            }
            
            .fashion-header h1 {
                font-size: 2rem;
            }
            
            .video-grid {
                display: none;
            }
            
            .mobile-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin-bottom: 30px;
            }
            
            .featured-video {
                margin-bottom: 30px;
            }
            
            .video-info {
                padding: 15px;
            }
            
            .video-title {
                font-size: 1rem;
            }
            
            .video-stats {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .video-player {
                display: none; /* На мобильных скрываем автозапуск */
            }
            
            .play-overlay {
                opacity: 0.7; /* На мобильных всегда видна иконка */
            }
        }
        
        /* Desktop Pyramid Layout */
        @media (min-width: 769px) {
            .floor-1 .video-grid { grid-template-columns: 1fr; }
            .floor-2 .video-grid { grid-template-columns: repeat(2, 1fr); }
            .floor-3 .video-grid { grid-template-columns: repeat(3, 1fr); }
            .floor-4 .video-grid { grid-template-columns: repeat(4, 1fr); }
            .floor-5 .video-grid { grid-template-columns: repeat(5, 1fr); }
        }
        
        /* Loading State */
        .video-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 107, 157, 0.3);
            border-top: 3px solid #ff6b9d;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="fashion-container">
        <div class="fashion-header">
            <h1>Fashion Video Hub</h1>
            <p>Модная платформа для просмотра видео</p>
        </div>

        <div class="category-panel">
            <a href="{{ route('video.index', array_merge(request()->except('page', 'category'), ['category' => 'all'])) }}" class="category-chip {{ ($selectedCategory ?? 'all') === 'all' ? 'active' : '' }}">Все категории</a>
            @foreach(($categories ?? []) as $category)
                <a href="{{ route('video.index', array_merge(request()->except('page', 'category'), ['category' => $category])) }}" class="category-chip {{ ($selectedCategory ?? 'all') === $category ? 'active' : '' }}">{{ $category }}</a>
            @endforeach
        </div>
        
        @if(isset($videos[0]))
            <!-- Featured Video (самое популярное) -->
            <div class="featured-video">
                <div class="featured-label">🔥 ТОП ВИДЕО #1</div>
                <div class="video-card" data-slug="{{ $videos[0]->slug }}" data-video-url="{{ $videos[0]->video_url }}">
                    <div class="video-thumbnail">
                        <div class="rating-badge">
                            <span class="rating-number">#1</span>
                            <span class="rating-score">
                                ({{ number_format($videos[0]->views + $videos[0]->comments()->count() + $videos[0]->likes) }})
                            </span>
                        </div>
                        <img src="{{ $videos[0]->thumbnail_url }}" alt="{{ $videos[0]->title }}">
                        <div class="video-player">
                            <video muted preload="metadata" loop>
                                <source src="{{ $videos[0]->video_preview_url ?? $videos[0]->video_url }}" type="video/mp4">
                            </video>
                            <div class="video-loading" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <div class="play-overlay">
                            <div class="play-icon">▶</div>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title">{{ $videos[0]->title }}</h3>
                        <div class="video-meta">
                            <span>⏱️ {{ $videos[0]->formatted_duration }}</span>
                            <span>🎬 {{ $videos[0]->quality }}</span>
                        </div>
                        <div class="video-stats">
                            <div class="stat">👁️ {{ number_format($videos[0]->views) }} просмотров</div>
                            <div class="stat">💬 {{ $videos[0]->comments()->count() }} коммент.</div>
                            <div class="stat">⭐ {{ number_format($videos[0]->views + $videos[0]->comments()->count() + $videos[0]->likes) }} рейтинг</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        @if($videos->isEmpty())
            <div class="empty-state">
                По выбранной категории пока нет видео.
            </div>
        @endif

        <!-- Desktop Pyramid Layout -->
        @php
            $globalIndex = 0;
        @endphp
        @foreach($groupedVideos as $floorIndex => $floorVideos)
            @php
                $floorNumber = min($floorIndex + 1, 5);
                $floorSize = count($floorVideos);
                if($floorIndex >= 4) $floorNumber = 5;
            @endphp
            <div class="video-floor floor-{{ $floorNumber }}">
                <h3 class="floor-title">Этаж {{ $floorIndex + 1 }} • {{ $floorSize }} видео</h3>
                <div class="video-grid">
                    @foreach($floorVideos as $video)
                        @php
                            $globalIndex++;
                            $totalRating = $video->views + $video->comments()->count() + $video->likes;
                            $position = $globalIndex + 1; // +1 потому что первое видео в featured
                        @endphp
                        @if($loop->parent->index >= 1 || $loop->index >= 1) <!-- Пропускаем первое видео первого этажа -->
                            <div class="video-card" data-slug="{{ $video->slug }}" data-video-url="{{ $video->video_url }}">
                                <div class="video-thumbnail">
                                    <div class="rating-badge">
                                        <span class="rating-number">
                                            {{ $position <= 3 ? '🔥 ' : '' }}#{{ $position }}
                                        </span>
                                        <span class="rating-score">
                                            ({{ number_format($totalRating) }})
                                        </span>
                                    </div>
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}">
                                    <div class="video-player">
                                        <video muted preload="metadata" loop>
                                            <source src="{{ $video->video_preview_url ?? $video->video_url }}" type="video/mp4">
                                        </video>
                                        <div class="video-loading" style="display: none;">
                                            <div class="spinner"></div>
                                        </div>
                                    </div>
                                    <div class="play-overlay">
                                        <div class="play-icon">▶</div>
                                    </div>
                                </div>
                                <div class="video-info">
                                    <h3 class="video-title">{{ $video->title }}</h3>
                                    <div class="video-meta">
                                        <span>⏱️ {{ $video->formatted_duration }}</span>
                                        <span>🎬 {{ $video->quality }}</span>
                                    </div>
                                    <div class="video-stats">
                                        <div class="stat">👁️ {{ number_format($video->views) }}</div>
                                        <div class="stat">💬 {{ $video->comments()->count() }}</div>
                                        <div class="stat">⭐ {{ number_format($totalRating) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
        
        <!-- Mobile Grid -->
        <div class="mobile-grid">
            @foreach($videos as $video)
                @php
                    $totalRating = $video->views + $video->comments()->count() + $video->likes;
                @endphp
                @if(!$loop->first) <!-- Пропускаем первое видео (оно featured) -->
                    <div class="video-card" data-slug="{{ $video->slug }}">
                        <div class="video-thumbnail">
                            <div class="rating-badge">
                                <span class="rating-number">#{{ $loop->iteration }}</span>
                                <span class="rating-score">
                                    ({{ number_format($totalRating) }})
                                </span>
                            </div>
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}">
                            <div class="play-overlay">
                                <div class="play-icon">▶</div>
                            </div>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">{{ $video->title }}</h3>
                            <div class="video-meta">
                                <span>⏱️ {{ $video->formatted_duration }}</span>
                                <span>🎬 {{ $video->quality }}</span>
                            </div>
                            <div class="video-stats">
                                <div class="stat">👁️ {{ number_format($video->views) }}</div>
                                <div class="stat">💬 {{ $video->comments()->count() }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($videos->hasPages())
            <div class="pagination">
                @if($videos->onFirstPage())
                    <span class="page-link">← Назад</span>
                @else
                    <a href="{{ $videos->previousPageUrl() }}" class="page-link">← Назад</a>
                @endif
                
                @foreach(range(1, min(5, $videos->lastPage())) as $page)
                    <a href="{{ $videos->url($page) }}" class="page-link {{ $videos->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                @if($videos->hasMorePages())
                    <a href="{{ $videos->nextPageUrl() }}" class="page-link">Вперед →</a>
                @else
                    <span class="page-link">Вперед →</span>
                @endif
            </div>
        @endif
    </div>
    
    <script>
        // Обработка кликов по видео карточкам
        document.addEventListener('DOMContentLoaded', function() {
            const videoCards = document.querySelectorAll('.video-card');
            
            videoCards.forEach(card => {
                const videoPlayer = card.querySelector('.video-player video');
                const loadingElement = card.querySelector('.video-loading');
                
                if (videoPlayer) {
                    // Предзагрузка видео
                    videoPlayer.load();
                    
                    // Обработчики для автозапуска при наведении
                    let hoverTimer;
                    let isVideoPlaying = false;
                    
                    card.addEventListener('mouseenter', function() {
                        if (window.innerWidth > 768) { // Только на десктопе
                            hoverTimer = setTimeout(() => {
                                if (loadingElement) loadingElement.style.display = 'flex';
                                
                                videoPlayer.play().then(() => {
                                    isVideoPlaying = true;
                                    if (loadingElement) loadingElement.style.display = 'none';
                                }).catch(error => {
                                    console.log('Ошибка автозапуска:', error);
                                    if (loadingElement) loadingElement.style.display = 'none';
                                });
                            }, 300); // Задержка 300мс перед запуском
                        }
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        clearTimeout(hoverTimer);
                        
                        if (isVideoPlaying) {
                            videoPlayer.pause();
                            videoPlayer.currentTime = 0;
                            isVideoPlaying = false;
                        }
                        
                        if (loadingElement) loadingElement.style.display = 'none';
                    });
                    
                    // Обработка клика для перехода на страницу видео
                    card.addEventListener('click', function(e) {
                        e.preventDefault();
                        const slug = this.getAttribute('data-slug');
                        if (slug) {
                            // Остановить все видео перед переходом
                            document.querySelectorAll('.video-player video').forEach(v => {
                                v.pause();
                                v.currentTime = 0;
                            });
                            
                            window.location.href = '/video/' + slug;
                        }
                    });
                }
            });
            
            // Остановить все видео при уходе со страницы
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    document.querySelectorAll('.video-player video').forEach(video => {
                        video.pause();
                        video.currentTime = 0;
                    });
                }
            });
            
            // Остановить все видео при скролле (опционально)
            let scrollTimer;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    document.querySelectorAll('.video-player video').forEach(video => {
                        const rect = video.getBoundingClientRect();
                        // Если видео не в видимой области экрана
                        if (rect.bottom < 0 || rect.top > window.innerHeight) {
                            video.pause();
                            video.currentTime = 0;
                        }
                    });
                }, 100);
            });
        });
    </script>
</body>
</html>