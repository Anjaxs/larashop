<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if (!request()->is('admin/*')) {
        //     \DB::listen(function ($query) {
        //         $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
        //         $bindings = $query->connection->prepareBindings($query->bindings);
        //         $pdo = $query->connection->getPdo();
        //         \Log::info($query->time . 'ms | ' . vsprintf($sqlWithPlaceholders, array_map([$pdo, 'quote'], $bindings)));
        //     });
        // }

        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
