<?php

use App\Actions\Structure\CreateStructureRecord;
use App\Http\Controllers\StructureController;
use App\Http\Middleware\EnsureStructureDemo;
use App\Support\JourneyPreview;
use Illuminate\Support\Facades\Route;

if (app()->environment(['local', 'testing'])) {
    Route::redirect('/', '/identidade-visual');
    Route::view('/identidade-visual', 'design-system')->name('design-system');
    Route::get('/jornadas', fn () => view('journeys', ['preview' => JourneyPreview::data()]))->name('journeys');
    Route::middleware(EnsureStructureDemo::class)->group(function () {
        Route::get('/estrutura', [StructureController::class, 'index'])->name('structure.index');
        Route::post('/estrutura/{type}', [StructureController::class, 'store'])->whereIn('type', CreateStructureRecord::TYPES)->name('structure.store');
    });
}
