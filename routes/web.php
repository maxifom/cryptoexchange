<?php
use Illuminate\Support\Facades\Auth;

Auth::routes();
Route::get('/emailConfirm', "EmailConfirmationController@confirmEmailView")->name('confirmEmailView')->middleware('guest');
Route::post('/emailConfirm', "EmailConfirmationController@confirmEmail")->name('confirmEmail')->middleware('guest');
Route::get('/emailConfirmToken/{token}', "EmailConfirmationController@confirmEmailToken")->name('confirmEmailToken')->middleware('guest');

Route::get('/news/{page?}', "NewsController@news")->name('news');

Route::get('/2fa', 'PasswordSecurityController@show2faForm');
Route::post('/generate2faSecret', 'PasswordSecurityController@generate2faSecret')->name('generate2faSecret')->middleware('auth');
Route::post('/2fa', 'PasswordSecurityController@enable2fa')->name('enable2fa')->middleware('auth');
Route::post('/disable2fa', 'PasswordSecurityController@disable2fa')->name('disable2fa')->middleware('auth');
Route::post('/2faVerify', function () {
    return redirect(URL()->previous());
})->name('2faVerify')->middleware('2fa');


Route::get('/confirmIp', "IpConfirmationController@ipConfirmView")->name('ipConfirmView');
Route::get('/confirmIpToken/{token}', "IpConfirmationController@confirmIpToken")->name('confirmIpToken');
Route::post('/confirmIp', "IpConfirmationController@confirmIp")->name('confirmIp');
Route::get('/', "MarketController@main")->name('index');
Route::get("/market/{base_coin}/{trade_coin}", "MarketController@market")->name('exchange');
Route::post('/getTrades', "MarketController@getTrades")->name('getTrades');
Route::get('/markets', "MarketController@markets")->name('markets');
Route::group(['middleware' => ['auth', '2fa']], function () {
    Route::get('/withdrawalConfirm', "WithdrawalConfirmationController@withdrawalConfirmView")->name('withdrawalConfirmView');
    Route::get('/withdrawalConfirmToken/{token}', "WithdrawalConfirmationController@withdrawalConfirmToken")->name('withdrawalConfirmToken');
    Route::get('/withdrawalCancelToken/{token}', "WithdrawalConfirmationController@rejectWithdrawaltoken")->name('withdrawalCancelToken');
    Route::post('/withdrawalCancel', "WithdrawalController@cancel")->name('withdrawalCancel');
    Route::post('/withdrawalConfirm', "WithdrawalConfirmationController@withdrawalConfirm")->name('withdrawalConfirm');

    Route::post('/createNewAddress', 'WalletController@createNewAddress')->name("newAddress");
    Route::get('/wallets', "UserInfoController@wallets")->name('wallets');

    Route::get('/withdraw/{coin_name}', "WithdrawalController@withdraw")->name('withdraw');
    Route::post('/withdraw', 'WalletController@withdraw')->name('withdrawPost');

    Route::post('/exchange', "TradeController@makeTrade")->name('exchange');

    Route::post('/deleteTrade', "TradeController@deleteTrade");

    Route::get('/depositHistory', "UserInfoController@depositHistory")->name('depositHistory');
    Route::post('/depositHistory', "UserInfoController@depositHistoryPOST")->name('depositHistoryPOST');

    Route::get('/trades', 'UserInfoController@trades')->name('trades');
    Route::post('/trades', "UserInfoController@tradesPOST")->name('tradesPOST');

    Route::get('/settings', "UserInfoController@settings")->name('settings');
    Route::post('/settings', "UserInfoController@settingsPOST")->name('settingsPOST');

    Route::get('/withdrawalHistory', "UserInfoController@withdrawalHistory")->name('withdrawalHistory');
    Route::post('/withdrawalHistory', "UserInfoController@withdrawalHistoryPOST")->name('withdrawalHistoryPOST');

    Route::get('/tradeHistory', "UserInfoController@tradeHistory")->name('tradeHistory');
    Route::post('/tradeHistory', "UserInfoController@tradeHistoryPOST")->name('tradeHistoryPOST');

    Route::get('/supportTickets/{page?}', "SupportTicketController@supportTickets")->name('supportTickets');
    Route::get('/createTicket', "SupportTicketController@createTicket")->name('createTicket');
    Route::post('/createTicket', "SupportTicketController@createTicketPOST")->name("createTicketPOST");
    Route::get('/ticket/{ticket}', "SupportTicketController@ticket")->name('ticket');
    Route::post('/addToTicket', "SupportTicketController@addToTicket")->name('addToTicket');
    Route::post('/closeTicket', "SupportTicketController@closeTicket")->name('closeTicket');

    Route::get('/apiCreate', "ApiEntryController@createView")->name('api_create_view');
    Route::post('/apiCreate', "ApiEntryController@create")->name('api_create');
    Route::get('/apiTokens', "ApiEntryController@tokens")->name('api_tokens');
    Route::get('/apiDocs', "ApiEntryController@docs")->name('api_docs');
    Route::post('/apiDelete', "ApiEntryController@delete")->name('api_delete');

});
Route::group([], function () {
    Route::get('/fundings', "CoinFundingController@fundings")->name('coin_fundings');
    Route::get('/funding/{id}', "CoinFundingController@funding")->name('coin_funding');
    Route::get('/fees',"FeeController@fees")->name('fees');
    Route::get('/cookie_policy',function(){
        return view('cookie_policy');
    })->name('cookie_policy');
    Route::get('/privacy_policy',function(){
        return view('privacy_policy');
    })->name('privacy_policy');
    Route::get('/terms_of_service',function(){
        return view('terms_of_service');
    })->name('terms_of_service');
    Route::get('/tutorial',function(){
        return view('tutorial');
    })->name('tutorial');
    Route::get('/resendEmailForm',function(){
        return view('resendEmailForm');
    })->name('resendEmail_form');
    Route::post('/resendEmail',"ResendController@resendEmailConfirmation")->name('resendEmail');
});
Route::group(['middleware' => ['auth', 'admin', '2fa']], function () {
    Route::prefix('admin')->group(function () {
        Route::get('/close',function(){
           $users = \App\User::all();
           foreach ($users as $user)
           {
               $user->notify(new \App\Notifications\TestNotification());
           }
        });



        Route::get('/dashboard', "AdminController@dashboard")->name('admin_dashboard');

        Route::get('/addCoin', "AdminController@addCoinView")->name('addCoinView');
        Route::post('/addCoin', "AdminController@addCoin")->name('addCoin');
        Route::get('/coins', "AdminController@coins")->name('coins');
        Route::get('/coinInfos', "CoinInfoController@coinInfos")->name('coinInfos');
        Route::post('/saveCoin', "AdminController@saveCoin")->name('saveCoin');
        Route::post('/checkWallet', "AdminController@checkWallet")->name('checkWallet');
        Route::post('/changeFee', "AdminController@changeFee")->name('admin_change_fee');
        Route::post('/makeFunding', "AdminController@makeFunding")->name('admin_make_funding');


        Route::get('/liveUpdates', "AdminController@liveUpdates")->name('liveUpdates');

        Route::get('/serverStats', 'AdminController@serverStats')->name('serverStats');

        Route::get('/supportTickets', 'AdminController@supportTickets')->name('AdminSupportTickets');
        Route::get('/ticket/{ticket}', "AdminController@ticket")->name('admin_ticket');
        Route::post('/answerTicket', "AdminController@answerTicket")->name('admin_answer_ticket');
        Route::post('/closeTicket', "AdminController@closeTicket")->name('admin_close_ticket');

        Route::get('/news/{page?}', 'AdminController@news')->name('admin_news');
        Route::get('/addNews', "AdminController@addNewsForm")->name('admin_add_news_form');
        Route::post('/addNews', "AdminController@addNews")->name('admin_add_news');
        Route::post('/changeNews', "AdminController@changeNews")->name('admin_change_news');
        Route::post('/makeDev', "AdminController@makeDev")->name('admin_make_dev');
        Route::get('/users', "AdminController@users")->name('admin_users');

        Route::get('/requests', "AdminController@requests")->name('admin_requests');
        Route::post('/reviewRequest', "AdminController@reviewRequest")->name('admin_reviewRequest');
        Route::post('/confirmRequest', "AdminController@confirmRequest")->name('admin_confirmRequest');
        Route::post('/requestToCoin', "AdminController@requestToCoin")->name('admin_request_to_coin');
        Route::post('/deleteRequest', "AdminController@deleteRequest")->name('admin_delete_request');
    });
});

Route::group(['middleware' => ['auth', 'dev', '2fa']], function () {
    Route::prefix('dev')->group(function () {
        Route::get('/coinRequests', "CoinRequestController@requests")->name('dev_requests');
        Route::get('/coinRequest', "CoinRequestController@requestForm")->name('dev_request_form');
        Route::post('/coinRequest', "CoinRequestController@request")->name('dev_request_coin');
        Route::post('/deleteRequest', "CoinRequestController@deleteRequest")->name('dev_request_delete');
    });
});
