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
        Route::get('/estrutura/nucleos', [StructureController::class, 'units'])->name('structure.units');
        Route::get('/estrutura/turmas', [StructureController::class, 'classrooms'])->name('structure.classrooms');
        Route::get('/estrutura/ofertas', [StructureController::class, 'offerings'])->name('structure.offerings');
        Route::get('/estrutura/curriculo', [StructureController::class, 'curriculum'])->name('structure.curriculum');
        Route::get('/estrutura/novo-cadastro', [StructureController::class, 'createIndex'])->name('structure.create.index');
        Route::get('/estrutura/novo-cadastro/{type}', [StructureController::class, 'create'])->whereIn('type', CreateStructureRecord::TYPES)->name('structure.create');
        Route::post('/estrutura/{type}', [StructureController::class, 'store'])->whereIn('type', CreateStructureRecord::TYPES)->name('structure.store');
    });
}
