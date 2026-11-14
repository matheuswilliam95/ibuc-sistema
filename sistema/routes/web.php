<?php

use App\Support\JourneyPreview;
use Illuminate\Support\Facades\Route;

if (app()->environment(['local', 'testing'])) {
    Route::redirect('/', '/identidade-visual');
    Route::view('/identidade-visual', 'design-system')->name('design-system');
    Route::get('/jornadas', fn () => view('journeys', ['preview' => JourneyPreview::data()]))->name('journeys');
}
