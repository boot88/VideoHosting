{{-- resources/views/video/show.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Video Player | Модный проигрыватель</title>
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
            padding: 20px;
            color: #fff;
        }
        
        .fashion-container {
            max-width: 1100px;
            margin: 0 auto;
            background: rgba(20, 20, 20, 0.9);
            border-radius: 24px;
            padding: 40px;
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
        
        /* Fashion Video Player */
        .fashion-player {
            width: 100%;
            margin-bottom: 40px;
            border-radius: 20px;
            overflow: hidden;
            background: #000;
            position: relative;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
        }
        
        #fashionVideo {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 20px;
        }
        
        /* Player Controls */
        .player-controls {
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
            padding: 25px;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            border-radius: 0 0 20px 20px;
        }
        
        .fashion-player:hover .player-controls {
            transform: translateY(0);
        }
        
        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-bottom: 20px;
            cursor: pointer;
            overflow: hidden;
        }
        
        .progress {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #ff6b9d, #4ecdc4);
            border-radius: 2px;
            transition: width 0.1s;
        }
        
        .control-buttons {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .left-controls, .right-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .control-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .control-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.1);
        }
        
        .play-btn {
            background: linear-gradient(45deg, #ff6b9d, #4ecdc4);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
        }
        
        .time-display {
            font-size: 14px;
            font-weight: 500;
            color: #ddd;
            letter-spacing: 1px;
        }
        
        .volume-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .volume-slider {
            width: 80px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            cursor: pointer;
        }
        
        .volume-progress {
            width: 70%;
            height: 100%;
            background: linear-gradient(90deg, #ff6b9d, #4ecdc4);
            border-radius: 2px;
        }
        
        /* Fashion Info Section */
        .fashion-info {
            background: rgba(30, 30, 30, 0.7);
            padding: 30px;
            border-radius: 20px;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .video-title {
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 15px;
            color: #fff;
        }
        
        .video-description {
            color: #aaa;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .video-meta {
            display: flex;
            gap: 30px;
            color: #888;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Loading Animation */
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #ff6b9d;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .fashion-container {
                padding: 20px;
            }
            
            .fashion-header h1 {
                font-size: 2rem;
            }
            
            .video-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="fashion-container">
        <div class="fashion-header">
            <h1>Fashion Video Player</h1>
            <p>Стильный проигрыватель для модного контента</p>
        </div>
        
        <div class="fashion-player">
            <div class="loading"></div>
            <video id="fashionVideo" preload="metadata">
                <source src="/images/funniest_home_videos_part_5.mp4" type="video/mp4">
                Ваш браузер не поддерживает видео тег.
            </video>
            
            <div class="player-controls">
                <div class="progress-bar" id="progressBar">
                    <div class="progress" id="progress"></div>
                </div>
                
                <div class="control-buttons">
                    <div class="left-controls">
                        <button class="control-btn play-btn" id="playBtn">
                            <span id="playIcon">▶</span>
                        </button>
                        <span class="time-display" id="currentTime">0:00</span>
                        <span class="time-display">/</span>
                        <span class="time-display" id="duration">0:00</span>
                    </div>
                    
                    <div class="right-controls">
                        <div class="volume-control">
                            <button class="control-btn" id="muteBtn">🔊</button>
                            <div class="volume-slider" id="volumeSlider">
                                <div class="volume-progress" id="volumeProgress"></div>
                            </div>
                        </div>
                        <button class="control-btn" id="fullscreenBtn">⛶</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="fashion-info">
            <h2 class="video-title">Funniest Home Videos Part 5</h2>
            <p class="video-description">
                Коллекция самых забавных домашних видео в стильном исполнении. Идеальное сочетание юмора и модной подачи.
            </p>
            <div class="video-meta">
                <div class="meta-item">
                    <span>📁 Формат:</span>
                    <span>MP4</span>
                </div>
                <div class="meta-item">
                    <span>🎥 Качество:</span>
                    <span>HD</span>
                </div>
                <div class="meta-item">
                    <span>📂 Размер:</span>
                    <span>~50 MB</span>
                </div>
                <div class="meta-item">
                    <span>📍 Источник:</span>
                    <span>Локальное видео</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('fashionVideo');
            const playBtn = document.getElementById('playBtn');
            const playIcon = document.getElementById('playIcon');
            const currentTimeEl = document.getElementById('currentTime');
            const durationEl = document.getElementById('duration');
            const progress = document.getElementById('progress');
            const progressBar = document.getElementById('progressBar');
            const muteBtn = document.getElementById('muteBtn');
            const volumeSlider = document.getElementById('volumeSlider');
            const volumeProgress = document.getElementById('volumeProgress');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const loading = document.querySelector('.loading');
            
            // Убираем индикатор загрузки
            video.addEventListener('loadeddata', function() {
                loading.style.display = 'none';
            });
            
            // Форматирование времени
            function formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            }
            
            // Обновление времени
            video.addEventListener('timeupdate', function() {
                currentTimeEl.textContent = formatTime(video.currentTime);
                const progressPercent = (video.currentTime / video.duration) * 100;
                progress.style.width = `${progressPercent}%`;
            });
            
            // Обновление общей длительности
            video.addEventListener('loadedmetadata', function() {
                durationEl.textContent = formatTime(video.duration);
                video.volume = 0.7;
                updateVolumeProgress();
            });
            
            // Управление воспроизведением
            playBtn.addEventListener('click', function() {
                if (video.paused) {
                    video.play();
                    playIcon.textContent = '⏸';
                } else {
                    video.pause();
                    playIcon.textContent = '▶';
                }
            });
            
            // Перемотка при клике на прогресс-бар
            progressBar.addEventListener('click', function(e) {
                const rect = progressBar.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                video.currentTime = pos * video.duration;
            });
            
            // Управление громкостью
            function updateVolumeProgress() {
                volumeProgress.style.width = `${video.volume * 100}%`;
                muteBtn.textContent = video.volume > 0 ? '🔊' : '🔇';
            }
            
            volumeSlider.addEventListener('click', function(e) {
                const rect = volumeSlider.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                video.volume = Math.max(0, Math.min(1, pos));
                updateVolumeProgress();
            });
            
            muteBtn.addEventListener('click', function() {
                video.muted = !video.muted;
                muteBtn.textContent = video.muted ? '🔇' : '🔊';
            });
            
            // Полноэкранный режим
            fullscreenBtn.addEventListener('click', function() {
                const player = document.querySelector('.fashion-player');
                
                if (!document.fullscreenElement) {
                    if (player.requestFullscreen) {
                        player.requestFullscreen();
                    } else if (player.webkitRequestFullscreen) {
                        player.webkitRequestFullscreen();
                    } else if (player.msRequestFullscreen) {
                        player.msRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                }
            });
            
            // Автовоспроизведение при клике на видео
            video.addEventListener('click', function() {
                if (video.paused) {
                    video.play();
                    playIcon.textContent = '⏸';
                } else {
                    video.pause();
                    playIcon.textContent = '▶';
                }
            });
            
            // Обновление иконки при окончании видео
            video.addEventListener('ended', function() {
                playIcon.textContent = '🔄';
            });
        });
    </script>
</body>
</html>