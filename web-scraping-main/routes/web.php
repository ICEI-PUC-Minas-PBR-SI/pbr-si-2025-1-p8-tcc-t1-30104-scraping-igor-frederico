<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelatorioController;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/patentes', function () {
        return view('patentes');
    })->name('patentes');

    Route::get('/inventores', function () {
        return view('inventores');
    })->name('inventores');


    Route::get('/claims', function () {
        return view('claims');
    })->name('claims');


    Route::get('/relatorios', function () {
        return view('relatorios');
    })->name('relatorios');

    Route::get('/controle', function () {
        return view('controle');
    })->name('controle');
});
