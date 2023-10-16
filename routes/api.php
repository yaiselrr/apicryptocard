<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**-------------------------------CLIENT ROUTES START---------------------------------------------------*/
Route::prefix('/v1/client')->namespace('App\Http\Controllers\Client')->group(function () {
    Route::post('/uploads', 'TemporalFileController@store')->name('uploads.store');
    Route::post('/uploads_base64', 'TemporalFileController@uploads_base64')->name('uploads.uploads_base64');
});

Route::prefix('/v1/client')->middleware('apivalidate.vixipay')->namespace('App\Http\Controllers\Client')->group(function () {

    Route::prefix('/mother_cards')->group(function () {
        Route::get('/', 'MotherCardController@index')->name('client.motherCards.index');
        Route::get('/{id}', 'MotherCardController@show')->name('client.motherCards.show');
        Route::get('/filter', 'MotherCardController@filter')->name('client.motherCards.filter');
        Route::post('/', 'MotherCardController@store')->name('client.motherCards.store');
        Route::put('/{id}', 'MotherCardController@update')->name('client.motherCards.update');
        Route::delete('/{id}', 'MotherCardController@destroy')->name('client.motherCards.destroy');
    });

    Route::prefix('/accounts')->group(function () {
        Route::get('/', 'AccountController@index')->name('client.accounts.index');
        Route::get('/{id}', 'AccountController@show')->name('client.accounts.show');
        Route::get('/filter/all', 'AccountController@filter')->name('client.accounts.filter');
        Route::post('/', 'AccountController@store')->name('client.accounts.store');
        Route::put('/{id}', 'AccountController@update')->name('client.accounts.update');
        Route::delete('/{id}', 'AccountController@destroy')->name('client.accounts.destroy');
        Route::post('/stolen', 'AccountController@reportStolenCard')->name('client.accounts.stolen');
        Route::post('/totals_accounts', 'AccountController@getTotalsAccounts')->name('client.accounts.getTotalsAccounts');
    });

    Route::prefix('/transactions')->group(function () {
        Route::get('/', 'TransactionController@index')->name('client.transactions.index');
        Route::get('/filter/all', 'TransactionController@filter')->name('client.transactions.filter');
        Route::post('/transfer_card_to_card', 'TransactionController@createTransactionTarjetaATarjeta')->name('client.transactions.createTransactionTarjetaATarjeta');
        Route::post('/transfer_mother_account_to_card', 'TransactionController@createTransactionCuentaMadreATarjeta')->name('client.transactions.createTransactionCuentaMadreATarjeta');
        Route::get('/movements/{id}', 'TransactionController@movements')->name('client.transactions.movements');
    });

    Route::prefix('/currencies')->group(function () {
        Route::get('/convert_mxn_to_usd', 'CurrencyController@convertMxnToUsd')->name('client.fees.index');
        Route::get('/convert_usd_to_mxn', 'CurrencyController@convertUsdToMxn')->name('client.fees.activeFee');
    });
});
/**-------------------------------CLIENT ROUTES END---------------------------------------------------*/






/**-------------------------------BACK ROUTES START---------------------------------------------------*/
Route::prefix('/v1/back')->namespace('App\Http\Controllers\Back')->group(function () {
});

Route::prefix('/v1/back')->middleware('apivalidate.vixipay')->namespace('App\Http\Controllers\Back')->group(function () {

    Route::prefix('/accounts')->group(function () {
        Route::get('/', 'AccountBackController@listAllAccounts')->name('back.accounts.listAllAccounts');
        Route::get('/{client_id}', 'AccountBackController@listAllClientAccount')->name('back.accounts.listAllClientAccount');
        Route::get('/filter/all', 'AccountBackController@filter')->name('back.accounts.filter');
        Route::post('/', 'AccountBackController@store')->name('back.accounts.store');
        Route::post('/stolen', 'AccountBackController@reportStolenCard')->name('back.accounts.stolen');
        Route::get('/list/{id}', 'AccountBackController@listAllAccountByCode')->name('back.accounts.listAllAccountByCode');
        Route::get('/show/data_collection_account', 'AccountBackController@dataCollectionAccount')->name('back.accounts.dataCollectionAccount');
    });

    Route::prefix('/transactions')->group(function () {
        Route::get('/', 'TransactionBackController@listTransactionsAll')->name('back.transactions.listTransactionsAll');
        Route::get('/list_mother_account', 'TransactionBackController@listTransactionsAccountMotherAll')->name('back.transactions.listTransactionsAccountMotherAll');
        Route::get('/list_card_to_card', 'TransactionBackController@listTransactionsCardToCardAll')->name('back.transactions.listTransactionsCardToCardAll');
        Route::get('/list_card_load', 'TransactionBackController@listTransactionsAllCardLoad')->name('back.transactions.listTransactionsAllCardLoad');
        Route::get('/filter/all', 'TransactionBackController@filter')->name('back.transactions.filter');
        Route::get('/filter/all/card_load', 'TransactionBackController@filtroAllCardLoad')->name('back.transactions.filtroAllCardLoad');
        Route::get('/filter/all/collection_account', 'TransactionBackController@filterCollectionAccount')->name('back.transactions.filterCollectionAccount');
        Route::get('/filter/movements', 'TransactionBackController@filterByAccount')->name('back.transactions.filterByAccount');
        Route::get('/list/movements', 'TransactionBackController@listMovementsByCardNumber')->name('back.transactions.listMovementsByCardNumber');
        Route::get('/consultar_estatus_tx', 'TransactionBackController@consultarTranferencia')->name('back.transactions.consultarTranferencia');
        Route::get('/consultar_todas_estatus_tx', 'TransactionBackController@consultarTranferenciasPendientes')->name('back.transactions.consultarTranferenciasPendientes');
        Route::post('/transfer_card_to_card', 'TransactionBackController@createTransactionTarjetaATarjeta')->name('back.transactions.createTransactionTarjetaATarjeta');
        Route::post('/load_card', 'TransactionBackController@loadCard')->name('back.transactions.loadCard');
        Route::get('/list_collection_account', 'TransactionBackController@listTransactionsCollectionSubAccount')->name('back.transactions.listTransactionsCollectionSubAccount');        
    });

    Route::prefix('/providers')->group(function () {
        Route::get('/', 'CardsProviderController@index')->name('back.providers.index');
        Route::get('/get_accounts/{card_provider_id}', 'CardsProviderController@getAccountsByProvider')->name('back.providers.getAccountsByProvider');
        Route::get('/get_collection_accounts/{card_provider_id}', 'CardsProviderController@getTransactionsCollectionAccountsByProvider')->name('back.providers.getTransactionsCollectionAccountsByProvider');
        Route::get('/get_transaction_accounts/{card_provider_id}', 'CardsProviderController@getTransactionsCardLoadByProvider')->name('back.providers.getTransactionsCardLoadByProvider');
        Route::get('/global_balance', 'CardsProviderController@globalBalance')->name('back.providers.globalBalance');
        Route::get('/mother_cards_provider/{card_provider_id}', 'CardsProviderController@cuentasMadresXProveeedor')->name('back.providers.cuentasMadresXProveeedor');
        Route::get('/filter/all/card_load/{card_provider_id}', 'CardsProviderController@filtroAllCardLoad')->name('back.transactions.filtroAllCardLoad');
    });    

    Route::prefix('/fees')->group(function () {
        Route::get('/', 'FeeBackController@index')->name('back.fees.index');
        Route::get('/active', 'FeeBackController@activeFee')->name('back.fees.activeFee');
        Route::get('/{id}', 'FeeBackController@show')->name('back.fees.show');
        Route::get('/fee_amount/{value}/{type}/{currency}/{account_id}', 'FeeBackController@getFeeFromAmount')->name('back.fees.getFeeFromAmount');
        Route::get('/filter/all', 'FeeBackController@filter')->name('back.fees.filter');
        Route::post('/', 'FeeBackController@store')->name('back.fees.store');
        Route::put('/{id}', 'FeeBackController@update')->name('back.fees.update');
        Route::delete('/{id}', 'FeeBackController@destroy')->name('back.fees.destroy');
    });

    Route::prefix('/transfer_limits')->group(function () {
        Route::get('/', 'TransferLimitBackController@index')->name('back.transferLimits.index');
        Route::get('/{id}', 'TransferLimitBackController@show')->name('back.transferLimits.show');
        Route::get('/filter', 'TransferLimitBackController@filter')->name('back.transferLimits.filter');
        Route::post('/', 'TransferLimitBackController@store')->name('back.transferLimits.store');
        Route::put('/{id}', 'TransferLimitBackController@update')->name('back.transferLimits.update');
        Route::delete('/{id}', 'TransferLimitBackController@destroy')->name('back.transferLimits.destroy');
        Route::get('/enabled/limit', 'TransferLimitBackController@enabledLimit')->name('back.transferLimits.enabledLimit');
    });

    Route::prefix('/fee_concepts')->group(function(){
        Route::get('/', 'FeeConceptController@index')->name('back.feeConcepts.index');
        Route::get('/{id}', 'FeeConceptController@show')->name('back.feeConcepts.show');
        Route::get('/{amount}/{concept_id}', 'FeeConceptController@getFeeFromAmountByConcept')->name('back.feeConcepts.getFeeFromAmountByConcept');
        
    });
    
    Route::prefix('/adjustments')->group(function(){
        Route::post('/', 'TransactionBackController@adjustment')->name('back.adjustments.store');
    });

    Route::prefix('/users')->group(function () {
        Route::get('/', 'TransactionBackController@users')->name('back.adjustments.users');
    });

    Route::prefix('/totals_collection_accounts')->group(function () {
        Route::get('/', 'TransactionBackController@totalsMovemenentsCollectionAccount')->name('back.totalsMovemenentsCollectionAccount.collectionAccount');
    });

    Route::prefix('/totals_mother_accounts')->group(function () {
        Route::get('/', 'TransactionBackController@totalsMovemenentsMotherAccount')->name('back.totalsMovemenentsMotherAccount.motherAccount');
    });
    Route::get('/by_concept/{concept_id}', 'FeeConceptController@getFeeByConcept')->name('back.feeConcepts.getFeeByConcept');
});
/**-------------------------------BACK ROUTES END---------------------------------------------------*/



/**-------------------------------WEBHOOKS ROUTES START---------------------------------------------------*/
Route::prefix('/v1/alquimia_webhooks')->namespace('App\Http\Controllers')->group(function () {
    Route::any('/{account_id}', 'AlquimiaWebhookController@webhook')->name('back.providers.index');
});
/**-------------------------------WEBHOOK ROUTES START---------------------------------------------------*/



/**-------------------------------API_VIXI ROUTES START---------------------------------------------------*/
Route::prefix('/v1/api_vixi')->middleware('apivalidate.vixipay')->namespace('App\Http\Controllers\Apivixi')->group(function () {
    Route::post('/client_register', 'VixiBackController@client_register')->name('api_vixi.client_register');
});
/**-------------------------------API_VIXI ROUTES START---------------------------------------------------*/
