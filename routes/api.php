<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('oauth/token', [
    'uses' => 'Passport\AccessTokenController@issueToken',
    'as' => 'passport.token',
    'middleware' => 'throttle',
]);

Route::post('register', 'RegistrationController@register')->name('api.register');
Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('api.sendResetLinkEmail');
Route::post('password/reset', 'ResetPasswordController@reset')->name('api.password.reset');

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('auth/logout', 'AuthenticationController@logout')->name("api.logout");

    Route::get('user-folders', 'UserFolderController@index')->name("api.user-folder.index");
    Route::post('user-folders', 'UserFolderController@create')->name("api.user-folder.create");
    Route::post('user-folders/subscribe', 'UserFolderController@createSubscription')->name("api.user-folder.createSubscription");
    Route::get('user-folders/{id}', 'UserFolderController@show')->name("api.user-folder.show");
    Route::post('user-folders/{id}', 'UserFolderController@update')->name("api.user-folder.update");
    Route::delete('user-folders/{id}', 'UserFolderController@destroy')->name("api.user-folder.destroy");
    Route::post('user-folders/unsubscribe/{userFolderSubscriptionId}', 'UserFolderController@unsubscribe')->name("api.user-folder.unsubscribe");
    Route::get('user-folders/subscriptions/all', 'UserFolderController@allSubscriptions')->name("api.user-folder.allSubscriptions");

    Route::get('articles', 'ArticleController@index')->name("api.articles.index");
    Route::get('sources', 'SourceController@index')->name("api.sources.index");
    Route::get('categories', 'CategoryController@index')->name("api.categories.index");
    Route::get('categories/{id}', 'CategoryController@show')->name("api.categories.show");

    Route::get('authors/{id}', 'AuthorController@show')->name("api.authors.show");

    Route::get('user-feed', 'UserFeedController@index')->name("api.categories.index");
});
