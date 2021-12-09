<?php

Route::get('/', 'DashboardController@index')->name('dashboard');

Route::post('/count-produto', 'DashboardController@countProduto')->name('countProduto');
Route::post('/count-venda', 'DashboardController@countVenda')->name('countVenda');

Route::post('/sincronizar', 'SincronizarController@sincroniza')->name('sincronizar');
Route::get('/sincronizar', 'SincronizarController@sincroniza')->name('sincronizar2');
