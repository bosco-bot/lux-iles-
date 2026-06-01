// Pages légales
Route::get('/politique-confidentialite', function () {
    return view('pages.politique-confidentialite');
})->name('politique-confidentialite');

Route::get('/politique-cookies', function () {
    return view('pages.politique-cookies');
})->name('politique-cookies');

Route::get('/mentions-legales', function () {
    return view('pages.mentions-legales');
})->name('mentions-legales');

Route::get('/cgv', function () {
    return view('pages.cgv');
})->name('cgv');