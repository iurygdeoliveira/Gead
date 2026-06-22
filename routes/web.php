<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\WebsiteLandingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['static'])->get('/', WebsiteLandingController::class)->name('home');

// Rota de compatibilidade para middlewares que usam route('login')
Route::get('/__compat-login', fn () => redirect()->to('/login'))->name('login');

// Google OAuth Routes
use App\Http\Controllers\Auth\GoogleLoginController;
Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');

// Página de solicitação de acesso
Route::get('/solicitar-acesso', function () {
    return view('filament.auth.solicitar-acesso');
})->name('solicitar-acesso');
