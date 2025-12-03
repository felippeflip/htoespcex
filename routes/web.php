<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/payment-requests', App\Livewire\PaymentRequestList::class)->name('payment-requests.index');
    Route::get('/payment-requests/create', App\Livewire\PaymentRequestForm::class)->name('payment-requests.create');
    Route::get('/monthly-report', App\Livewire\MonthlyReport::class)->name('monthly-report');
});

require __DIR__.'/auth.php';
