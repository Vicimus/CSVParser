<?php

Route::any('csvparser/start', array('as' => 'csvparser.start', 'uses' => 'Vicimus\CSVParser\Controllers\MainController@start'));
Route::any('csvparser/process', array('as' => 'csvparser.process', 'uses' => 'Vicimus\CSVParser\Controllers\MainController@process'));
Route::any('csvparser/finish', array('as' => 'csvparser.finish', 'uses' => 'Vicimus\CSVParser\Controllers\MainController@finish'));