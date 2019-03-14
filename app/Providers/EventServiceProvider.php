<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\NewBlock' => [
            'App\Listeners\NewBlockListener',
        ],
        'App\Events\TxConfirmation' => [
            'App\Listeners\TxConfirmationListener',
        ],
        'App\Events\TxConfirmed' => [
            'App\Listeners\TxConfirmedListener',
        ],
        'App\Events\TxReceived' => [
            'App\Listeners\TxReceivedListener',
        ],
        'App\Events\WithdrawalApproved' => [
            'App\Listeners\WithdrawalApprovedListener',
        ],
        'App\Events\WithdrawalRequested' => [
            'App\Listeners\WithdrawalRequestedListener',
        ],
        'App\Events\WithdrawalSent' => [
            'App\Listeners\WithdrawalSentListener',
        ],
        'App\Events\TradeUpdated'=>[
           'App\Listeners\TradeUpdatedListener'
        ],
        'App\Events\NewTrade'=>[
            'App\Listeners\NewTradeListener'
        ],
        'App\Events\TradeDeleted'=>[
          'App\Listeners\TradeDeletedListener'
        ],
        'App\Events\TradeSuccessful'=>[
            'App\Listeners\TradeSuccessfulListener'
        ],
        'App\Events\newUserTrade' => [
            'App\Listeners\newUserTradeListener',
        ],
        'App\Events\Update' => [
            'App\Listeners\UpdateListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
