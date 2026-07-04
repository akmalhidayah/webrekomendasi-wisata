<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FasilitasWisataController;
use App\Http\Controllers\Admin\FotoWisataController;
use App\Http\Controllers\Admin\HasilRekomendasiController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\KategoriWisataController;
use App\Http\Controllers\Admin\RatingKunjunganController as AdminRatingKunjunganController;
use App\Http\Controllers\Admin\SurveyPreferensiController as AdminSurveyPreferensiController;
use App\Http\Controllers\Admin\WisataController as AdminWisataController;
use App\Http\Controllers\Wisatawan\HomeController;
use App\Http\Controllers\Wisatawan\RatingKunjunganController;
use App\Http\Controllers\Wisatawan\RekomendasiController;
use App\Http\Controllers\Wisatawan\SurveyPreferensiController;
use App\Http\Controllers\Wisatawan\WisataController as WisatawanWisataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('wisatawan.home');
Route::get('/wisata', [WisatawanWisataController::class, 'index'])->name('wisatawan.wisata.index');
Route::get('/wisata/{slug}', [WisatawanWisataController::class, 'show'])->name('wisatawan.wisata.show');

Route::get('/rekomendasi/survey', [SurveyPreferensiController::class, 'index'])->name('wisatawan.survey.index');
Route::post('/rekomendasi/survey', [SurveyPreferensiController::class, 'store'])->name('wisatawan.survey.store');
Route::get('/rekomendasi/survey/success', [SurveyPreferensiController::class, 'success'])->name('wisatawan.survey.success');
Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('wisatawan.rekomendasi.index');
Route::post('/rekomendasi/proses', [RekomendasiController::class, 'proses'])->name('wisatawan.rekomendasi.proses');
Route::get('/rekomendasi/hasil', [RekomendasiController::class, 'hasil'])->name('wisatawan.rekomendasi.hasil');
Route::delete('/rekomendasi/reset', [RekomendasiController::class, 'reset'])->name('wisatawan.rekomendasi.reset');
Route::post('/rating-kunjungan', [RatingKunjunganController::class, 'store'])->name('wisatawan.rating-kunjungan.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.process');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/survey-preferensi', [AdminSurveyPreferensiController::class, 'index'])->name('survey-preferensi.index');
        Route::get('/hasil-rekomendasi', [HasilRekomendasiController::class, 'index'])->name('hasil-rekomendasi.index');
        Route::get('/hasil-rekomendasi/{guestVisitor}', [HasilRekomendasiController::class, 'show'])->name('hasil-rekomendasi.show');
        Route::get('/rating-kunjungan', [AdminRatingKunjunganController::class, 'index'])->name('rating-kunjungan.index');
        Route::get('/rating-kunjungan/{ratingKunjungan}', [AdminRatingKunjunganController::class, 'show'])->name('rating-kunjungan.show');
        Route::patch('/rating-kunjungan/{ratingKunjungan}/setujui', [AdminRatingKunjunganController::class, 'setujui'])->name('rating-kunjungan.setujui');
        Route::patch('/rating-kunjungan/{ratingKunjungan}/tolak', [AdminRatingKunjunganController::class, 'tolak'])->name('rating-kunjungan.tolak');
        Route::delete('/rating-kunjungan/{ratingKunjungan}', [AdminRatingKunjunganController::class, 'destroy'])->name('rating-kunjungan.destroy');

        Route::resource('kategori-wisata', KategoriWisataController::class)
            ->parameters(['kategori-wisata' => 'kategoriWisata']);
        Route::resource('hotels', HotelController::class);
        Route::resource('wisata', AdminWisataController::class)
            ->parameters(['wisata' => 'wisata']);

        Route::prefix('wisata/{wisata}')->name('wisata.')->group(function () {
            Route::get('/fasilitas', [FasilitasWisataController::class, 'index'])->name('fasilitas.index');
            Route::get('/fasilitas/create', [FasilitasWisataController::class, 'create'])->name('fasilitas.create');
            Route::post('/fasilitas', [FasilitasWisataController::class, 'store'])->name('fasilitas.store');
            Route::get('/fasilitas/{fasilitasWisata}/edit', [FasilitasWisataController::class, 'edit'])->name('fasilitas.edit');
            Route::put('/fasilitas/{fasilitasWisata}', [FasilitasWisataController::class, 'update'])->name('fasilitas.update');
            Route::delete('/fasilitas/{fasilitasWisata}', [FasilitasWisataController::class, 'destroy'])->name('fasilitas.destroy');

            Route::get('/foto', [FotoWisataController::class, 'index'])->name('foto.index');
            Route::post('/foto', [FotoWisataController::class, 'store'])->name('foto.store');
            Route::put('/foto/{fotoWisata}', [FotoWisataController::class, 'update'])->name('foto.update');
            Route::delete('/foto/{fotoWisata}', [FotoWisataController::class, 'destroy'])->name('foto.destroy');
            Route::patch('/foto/{fotoWisata}/set-utama', [FotoWisataController::class, 'setUtama'])->name('foto.set-utama');
        });
    });
});
