<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/create',[\App\Http\Controllers\PokazController::class,'create'])->name('pokaz.create');
Route::get('/create/{flag_prev}',[\App\Http\Controllers\PokazController::class,'create'])->name('pokaz.create_flag');
Route::post('/store',[\App\Http\Controllers\PokazController::class,'store'])->name('pokaz.store');

Route::get('/createEmail',[\App\Http\Controllers\EmailController::class,'create'])->name('email.create');
Route::post('/storeEmail',[\App\Http\Controllers\EmailController::class,'store'])->name('email.store');

Route::get('/createFlat',[\App\Http\Controllers\FlatController::class,'create'])->name('flat.create');
Route::post('/storeFlat',[\App\Http\Controllers\FlatController::class,'store'])->name('flat.store');

Route::get('/createTarif',[\App\Http\Controllers\TarifController::class,'create'])->name('tarif.create');
Route::post('/storeTarif',[\App\Http\Controllers\TarifController::class,'store'])->name('tarif.store');
Route::get('/editTarif',[\App\Http\Controllers\TarifController::class,'edit'])->name('tarif.edit');
Route::post('/updateTarif',[\App\Http\Controllers\TarifController::class,'update'])->name('tarif.update');

Route::get('/calc',[\App\Http\Controllers\PokazController::class,'calc'])->name('pokaz.calc');

Route::get('/list/{user_id}',[\App\Http\Controllers\PokazController::class,'list'])->name('pokaz.list');

//создание pdf-документов
Route::get('pdf/preview', [\App\Http\Controllers\GenerateController::class, 'preview'])->name('pdf.preview');
Route::get('pdf/generate/{month}', [\App\Http\Controllers\GenerateController::class, 'generatePDF'])->name('pdf.generate');

//тестовый роут для экспериментов
Route::get('/test',[\App\Http\Controllers\TestController::class,'index'])->name('test.index');

require __DIR__.'/auth.php';
