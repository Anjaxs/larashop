<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('test_helper')) {
    function test_helper()
    {
        echo 'OK';
    }
}

if (! function_exists('route_class')) {
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }
}
