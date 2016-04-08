<?php

namespace Boodschappen\Providers;

use Boodschappen\Crawling\DataSources\Dekamart;
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

    private $product_sources = [
        AlbertHeijn::class,
        Dekamart::class,
        Jumbo::class,
        Hoogvliet::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach($this->product_sources as $source) {
            $this->app->bind($source);
        }

        $this->app->singleton('\NumberFormatter', function() {
            $numberFormatter = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
            return $numberFormatter;
        });
    }
}
