<?php

//use App\Events\MessageSent;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatController;
//use App\Models\Message;
use Illuminate\Support\Facades\Route;

//Route::get('/test-broadcast', function () {
//    broadcast(new MessageSent('Привет!'))->toOthers();
//    return 'Event broadcasted!';
//});
//
//Route::get('/test-broadcast', function () {
//    $message = Message::latest()->first()->load('user', 'translations');
//    broadcast(new MessageSent($message));
//    return 'ok';
//});

Route::get('/', [ChatController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
