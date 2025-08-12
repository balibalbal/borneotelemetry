<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// dimatiin dulu 25-08-2024
// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('alarm-channel.{customer_id}', function ($user, $customer_id) {
    // Hanya izinkan pengguna dengan customer_id yang sama
    return (int) $user->customer_id === (int) $customer_id;
});
