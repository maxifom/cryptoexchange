<?php

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
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('block-tracker', function () {
    return false;
});

Broadcast::channel('balances.{id}',function($user,$id)
{
    return (int)$user->id === (int)$id;
});
Broadcast::channel('trades.{id}',function($user,$id)
{
    return (int)$user->id === (int)$id;
});
Broadcast::channel('admin',function($user,$id)
{
    \Illuminate\Support\Facades\Storage::append('admin_logins',time()." ".$user->id);
    return $user->admin===1;
});