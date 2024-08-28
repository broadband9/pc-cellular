<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-email', function () {
    try {
        Mail::to('muazzam.zulfiqar123@gmail.com')->send(new \App\Mail\CustomRepairEmail(\App\Models\Repair::first()));
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
});
