<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::bind('poll', function($id) {
    return App\Models\Poll::with('questions.options.users')->findOrFail($id);
});

Route::get('/', 'HomeController@index')->name('welcome');
Route::get('polls', 'PollController@index')->name('polls');
Route::get('poll/{poll}', 'PollController@show')->name('poll')
    ->middleware(['not-answered', 'deadline-expired']);

Route::post('poll/{poll}', 'PollController@store')->name('store-poll')
    ->middleware(['auth', 'not-answered', 'deadline-expired']);

Route::get('result/{poll}', 'ResultController@show')->name('result')->middleware(['auth']);

Route::group(['namespace' => 'Auth', 'as' => 'auth.'], function () {

    Route::get('login', 'LoginController@showLoginForm')->name('login')->middleware('guest');

    /*
     * These routes require the user to be logged in
     */
    Route::group(['middleware' => 'auth'], function () {
        Route::get('logout', 'LoginController@logout')->name('logout');
    });

    /*
     * These routes require no user to be logged in
     */
    Route::group(['middleware' => 'guest'], function () {
        // Socialite Routes
        Route::get('login/{provider}', 'UserController@login')->name('social.login');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'namespace' => 'Admin'], function () {
    CRUD::resource('poll', 'PollCrudController');

    Route::group(['prefix' => 'poll/{poll_id}'], function()
    {
        CRUD::resource('question', 'QuestionCrudController');
    });

    Route::group(['prefix' => 'poll/{poll_id}/question/{question_id}'], function()
    {
        CRUD::resource('option', 'OptionCrudController');
    });
});