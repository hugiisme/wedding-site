<?php

use App\Http\Controllers\RsvpController;
use App\Http\Controllers\RsvpSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/rsvp', [RsvpController::class, 'store']);

// Trang tổng hợp & thống kê phản hồi RSVP (truy cập qua URL, không có link điều hướng)
Route::get('/rsvp-summary', [RsvpSummaryController::class, 'index'])->name('rsvp.summary');
Route::get('/rsvp-summary/export', [RsvpSummaryController::class, 'export'])->name('rsvp.export');
