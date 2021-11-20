<?php

Route::get('/', 'DashboardController@index')->name('dashboard');
Route::get('/sincronizar', 'SincronizarController@sincroniza')->name('sincronizar');
