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
        
        /* Featured Video */
        .featured-video {
            margin-bottom: 50px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
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
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
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
        }
        
        /* Desktop Pyramid Layout */
        @media (min-width: 769px) {
            .floor-1 .video-grid { grid-template-columns: 1fr; }
            .floor-2 .video-grid { grid-template-columns: repeat(2, 1fr); }
            .floor-3 .video-grid { grid-template-columns: repeat(3, 1fr); }
            .floor-4 .video-grid { grid-template-columns: repeat(4, 1fr); }
            .floor-5 .video-grid { grid-template-columns: repeat(5, 1fr); }
        }
    </style>
</head>
<body>
    <div class="fashion-container">
        <div class="fashion-header">
            <h1>Fashion Video Hub</h1>
            <p>Модная платформа для просмотра видео</p>
        </div>
        
        @if(isset($videos[0]))
            <!-- Featured Video -->
            <div class="featured-video">
                <div class="featured-label">ТОП ВИДЕО</div>
                <div class="video-card" onclick="window.location.href='{{ route('video.show', $videos[0]->slug) }}'">
                    <div class="video-thumbnail">
                        <div class="rating-badge">🔥 #1</div>
                        <img src="{{ $videos[0]->thumbnail_url }}" alt="{{ $videos[0]->title }}">
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
                            <div class="stat">👁️ {{ $videos[0]->views }} просмотров</div>
                            <div class="stat">💬 {{ $videos[0]->comments_count ?? 0 }} коммент.</div>
                            <div class="stat">⭐ {{ $videos[0]->rating }} рейтинг</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Desktop Pyramid Layout -->
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
                        @if($loop->index >= 1 || $floorIndex > 0) <!-- Пропускаем первое видео (оно featured) -->
                            <div class="video-card" onclick="window.location.href='{{ route('video.show', $video->slug) }}'">
                                <div class="video-thumbnail">
                                    <div class="rating-badge">#{{ ($floorIndex * 5) + $loop->index + 1 }}</div>
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
                                        <div class="stat">👁️ {{ $video->views }}</div>
                                        <div class="stat">💬 {{ $video->comments_count ?? 0 }}</div>
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
                @if(!$loop->first) <!-- Пропускаем первое видео -->
                    <div class="video-card" onclick="window.location.href='{{ route('video.show', $video->slug) }}'">
                        <div class="video-thumbnail">
                            <div class="rating-badge">#{{ $loop->iteration }}</div>
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
                                <div class="stat">👁️ {{ $video->views }}</div>
                                <div class="stat">💬 {{ $video->comments_count ?? 0 }}</div>
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
        // Fullscreen video click
        document.querySelectorAll('.video-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('a')) return; // Если клик по ссылке
                window.location.href = this.getAttribute('onclick').match(/'(.*?)'/)[1];
            });
        });
        
        // Auto-play first video on hover (optional)
        document.querySelector('.featured-video .video-card').addEventListener('mouseenter', function() {
            // Можно добавить превью-анимацию
        });
    </script>
</body>
</html>