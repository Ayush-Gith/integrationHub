<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->singleton(ShopifyClient::class, function($app){
            return new ShopifyClient(config('services.shopify'));
        });
    }
}
