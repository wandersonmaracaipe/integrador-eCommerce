<?php

Route::get('/', 'DashboardController@index')->name('dashboard');
Route::post('/sincronizar', 'SincronizarController@sincroniza')->name('sincronizar');
