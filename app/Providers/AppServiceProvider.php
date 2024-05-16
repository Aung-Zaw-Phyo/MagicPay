<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
    public function boot(UrlGenerator $url)
    {
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            $unread_noti_count = 0;

            if(Auth::guard('web')->check()){
                $unread_noti_count = Auth::guard('web')->user()->unreadNotifications()->count();
            }

            View::share([
                'unread_noti_count' => $unread_noti_count,
            ]);
        });

        if (env('APP_ENV') !== 'local') { 
            $url->forceScheme('https');
        }
    }
}
