<?php

namespace App\Providers;

use App\Models\TravelOrder;
use App\Observers\TravelOrderObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ($this->app->environment('production')) && URL::forceScheme('https');

        Passport::tokensExpireIn(now()->addMonths(2));
        Passport::tokensCan([
            'user-permission'   => 'Permissões de usuário',
            'admin-permission'  => 'Permisssões de administrador',
        ]);

        TravelOrder::observe(TravelOrderObserver::class);
    }
}
