<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('sales', SaleController::class);

    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Reports routes
    Route::get('/reports/sales', [App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/revenue', [App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/export/{type}', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});
