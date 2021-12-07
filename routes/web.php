<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
//use App\Http\Controllers;

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
Route::post('/store',[\App\Http\Controllers\PokazController::class,'store'])->name('pokaz.store');

Route::get('/calc',[\App\Http\Controllers\PokazController::class,'calc'])->name('pokaz.calc');

Route::get('/list',[\App\Http\Controllers\PokazController::class,'list'])->name('pokaz.list');
Route::post('/info',[\App\Http\Controllers\PokazController::class,'info'])->name('pokaz.info');


//создание pdf-документов
Route::get('pdf/preview', [\App\Http\Controllers\GenerateController::class, 'preview'])->name('pdf.preview');
Route::get('pdf/generate/{month}', [\App\Http\Controllers\GenerateController::class, 'generatePDF'])->name('pdf.generate');

//тестовый роут для экспериментов
Route::get('/test',[\App\Http\Controllers\TestController::class,'index'])->name('test.index');

// Admin Panel
Route::group(['prefix' => 'admin-panel'], function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index'); // 'middleware' => ['auth', 'admin-panel']

    // Pokazs
    Route::prefix('pokazs')->group(function () {
        Route::get('create', [\App\Http\Controllers\AdminController::class,'adminCreate'])->name('pokaz.adminCreate');
        Route::post('store',  [\App\Http\Controllers\AdminController::class,'adminStore'])->name('pokaz.adminStore');
    });
    // Counter
    Route::prefix('counter')->group(function () {
        Route::get('create', [\App\Http\Controllers\CounterController::class,'create'])->name('counter.create');
        Route::post('store',  [\App\Http\Controllers\CounterController::class,'store'])->name('counter.store');
    });
    // Email
    Route::prefix('email')->group(function () {
        Route::get('/create', [\App\Http\Controllers\EmailController::class, 'create'])->name('email.create');
        Route::post('/store', [\App\Http\Controllers\EmailController::class, 'store'])->name('email.store');
    });
    // Flat
    Route::prefix('flat')->group(function () {
        Route::get('/create',[\App\Http\Controllers\FlatController::class,'create'])->name('flat.create');
        Route::post('/store',[\App\Http\Controllers\FlatController::class,'store'])->name('flat.store');
    });
    // Tarif
    Route::prefix('tarif')->group(function () {
        Route::get('/create',[\App\Http\Controllers\TarifController::class,'create'])->name('tarif.create');
        Route::post('/store',[\App\Http\Controllers\TarifController::class,'store'])->name('tarif.store');
        Route::get('/edit',[\App\Http\Controllers\TarifController::class,'edit'])->name('tarif.edit');
        Route::post('/update',[\App\Http\Controllers\TarifController::class,'update'])->name('tarif.update');
    });

});

require __DIR__.'/auth.php';
