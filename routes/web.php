<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', [VideoController::class, 'index'])->name('video.index');
Route::get('/video/{slug}', [VideoController::class, 'show'])->name('video.show');
Route::get('/video/{slug}/fullscreen', [VideoController::class, 'fullscreen'])->name('video.fullscreen');
Route::post('/video/{slug}/comment', [VideoController::class, 'storeComment'])->name('video.comment.store');