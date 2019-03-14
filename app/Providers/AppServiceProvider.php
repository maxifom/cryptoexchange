<?php

namespace App\Providers;
use App\Coin;
use App\CoinInfo;
use App\Deposit;
use App\Fee;
use App\Market;
use App\MarketHistory;
use App\Trade;
use App\UserTrade;
use App\Wallet;
use App\Withdrawal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Observers\CoinObserver;
use App\Observers\CoinInfoObserver;
use App\Observers\DepositObserver;
use App\Observers\FeeObserver;
use App\Observers\MarketObserver;
use App\Observers\MarketHistoryObserver;
use App\Observers\TradeObserver;
use App\Observers\UserTradeObserver;
use App\Observers\WalletObserver;
use App\Observers\WithdrawalObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Coin::observe(CoinObserver::class);
        CoinInfo::observe(CoinInfoObserver::class);
        Deposit::observe(DepositObserver::class);
        Fee::observe(FeeObserver::class);
        Market::observe(MarketObserver::class);
        MarketHistory::observe(MarketHistoryObserver::class);
        Trade::observe(TradeObserver::class);
        UserTrade::observe(UserTradeObserver::class);
        Wallet::observe(WalletObserver::class);
        Withdrawal::observe(WithdrawalObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
