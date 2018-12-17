<?php
use App\Trade;
use App\Portfolio;
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

Broadcast::channel('trades.{tradeId}', function ($user, $tradeId) {
    return (int) $user->id === (int) Trade::find($tradeId)->user_id;
});
Broadcast::channel('portfolios.{portfolioId}', function ($user, $portfolioId) {
    return (int) $user->id === (int) Portfolio::find($portfolioId)->user_id;
});
Broadcast::channel('assets.{portfolioId}', function ($user, $portfolioId) {
    return (int) $user->id === (int) Portfolio::find($portfolioId)->user_id;
});