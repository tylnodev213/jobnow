<?php

use App\Http\Controllers\ContactsController;
use Illuminate\Support\Facades\Route;

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

Route::get('/',[ContactsController::class, 'index'])->name('index');
Route::get('/create',[ContactsController::class, 'create'])->name('create');
Route::post('/create',[ContactsController::class, 'store'])->name('store');
Route::get('/edit/{id}',[ContactsController::class, 'edit'])->name('edit');
Route::post('/update/{id}',[ContactsController::class, 'update'])->name('update');
Route::post('/delete/{id}',[ContactsController::class, 'destroy'])->name('destroy');
