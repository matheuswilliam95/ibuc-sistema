<?php

use Illuminate\Support\Facades\Route;

if (app()->environment(['local', 'testing'])) {
    Route::redirect('/', '/identidade-visual');
    Route::view('/identidade-visual', 'design-system')->name('design-system');
}
