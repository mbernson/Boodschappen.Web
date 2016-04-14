<?php

namespace Boodschappen\Providers;

use Boodschappen\Crawling\DataSources\Dekamarkt;
use Illuminate\Support\ServiceProvider;

use Boodschappen\Crawling\DataSources\Hoogvliet;
use Boodschappen\Crawling\DataSources\AlbertHeijn;
use Boodschappen\Crawling\DataSources\Jumbo;

use NumberFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $product_sources = config('boodschappen.product_sources');
        foreach($product_sources as $source) {
            $this->app->bind($source);
        }

        $this->app->singleton('\NumberFormatter', function() {
            $numberFormatter = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
            return $numberFormatter;
        });
    }
}
