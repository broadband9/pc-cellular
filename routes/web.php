<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;

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

Route::get('/printers', [PrintController::class, 'index'])->name('print.index');
Route::post('/print', [PrintController::class, 'print'])->name('print.submit');
Route::get('/check-cups', function() {
    $results = [
        'vendor_directory_exists' => is_dir(base_path('vendor/smalot/cups-ipp')),
        'class_file_exists' => is_file(base_path('vendor/smalot/cups-ipp/src/IPP.php')),
        'autoload_file_contains_class' => false,
        'class_exists' => class_exists('Smalot\Cups\IPP'),
        'composer_json_has_package' => false
    ];
    
    // Check composer.json
    $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
    $results['composer_json_has_package'] = isset($composerJson['require']['smalot/cups-ipp']);
    
    // Check autoload files
    $autoloadFile = file_get_contents(base_path('vendor/composer/autoload_classmap.php'));
    $results['autoload_file_contains_class'] = strpos($autoloadFile, 'Smalot\\Cups\\IPP') !== false;
    
    return $results;
});