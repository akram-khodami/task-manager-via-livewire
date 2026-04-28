<?php

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\Dashboard;
use App\Livewire\Folders;
use App\Livewire\Home;
use App\Livewire\ProjectDetail;
use App\Livewire\Projects;
use App\Livewire\Tasks;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->middleware(['setLocale']);
Route::get('/register', RegisterForm::class)->name('register')->middleware(['setLocale']);
Route::get('/login', LoginForm::class)->name('login')->middleware(['setLocale']);;
Route::middleware(['auth:sanctum', 'setLocale'])->group(function () {
    Route::get('/dashboard', Dashboard::class);
    Route::get('/users', Users::class);

    Route::get('projects', Projects::class)->name('projects');//پروژه ها
    Route::get('/projects/{projectId}', ProjectDetail::class)->name('projects.show');//جزییات
    Route::get('/folders/{projectId?}', Folders::class)->name('folders.index');//پوشه ها
    Route::get('/tasks', Tasks::class)->name('tasks.index');//تسک ها

//    Route::get('/projects/{projectId}/folders/{folderId}', ProjectDetail::class)->name('projects.show');

});


// تنظیم زبان پیش‌فرض
Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['fa', 'en'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('set-locale');
