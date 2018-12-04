<?php

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'LineController@app')->name('line');
Route::resource('messages', 'LineController');

