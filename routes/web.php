<?php

Route::get('/', 'ProfileController@index');

Auth::routes(['register'=>false]);

Route::get('/profiles/{profile}/versions', 'VersionsController@index');
Route::get('/profiles/{profile}/versions/create', 'VersionsController@store');
Route::get('/versions/{version}/destroy', 'VersionsController@destroy');
Route::get('/deploy/{version}', 'DeploymentsController');
Route::resource('/profiles', 'ProfileController', ['only'=>['create', 'store', 'update', 'edit']]);
Route::get('/profiles/{profile}/delete', 'ProfileController@destroy');
Route::resource('/profiles/{profile}/replacement/', 'ReplacementController', ['only'=>['index', 'create', 'store', 'update']]);
Route::resource('/replacement', 'ReplacementController', ['only'=>['update', 'edit']]);
Route::get('/replacement/{replacement}/delete', 'ReplacementController@destroy');
