<?php

return [
    'ffmpeg' => [
        'ffmpeg_path' => env('FFMPEG_PATH', 'C:\Soz\laragon\bin\ffmpeg\bin\ffmpeg.exe'),
        'ffprobe_path' => env('FFPROBE_PATH', 'C:\Soz\laragon\bin\ffmpeg\bin\ffprobe.exe'),
        'timeout' => 3600,
        'threads' => 12,
    ],
];