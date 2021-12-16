<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

if (!function_exists('route_class')) {
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }
}

if (!function_exists('ngrok_url')) {
    function ngrok_url($routeName, $parameters = [])
    {
        // 开发环境，并且配置了 NGROK_URL
        if (app()->environment('local') && $url = config('app.ngrok_url')) {
            // route() 函数第三个参数代表是否绝对路径
            return $url . route($routeName, $parameters, false);
        }

        return route($routeName, $parameters);
    }
}

if (!function_exists('sql_log')) {
    function sql_log()
    {
        if (!config('app.open_sql_log') || request()->is('admin/*')) {
            return;
        }
        $i = 0;
        DB::listen(function ($query) use (&$i) {
            $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
            $bindings = $query->connection->prepareBindings($query->bindings);
            $pdo = $query->connection->getPdo();
            Log::info("#$i " . vsprintf($sqlWithPlaceholders, array_map([$pdo, 'quote'], $bindings)) . " ($query->time ms)");
            $i++;
        });
    }
}
