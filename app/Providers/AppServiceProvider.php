<?php

namespace App\Providers;

use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TicketRepository::class, function ($app) {
            return new TicketRepository($app->make(\App\Models\Ticket::class));
        });
    
        $this->app->bind(TicketService::class, function ($app) {
            return new TicketService($app->make(TicketRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
