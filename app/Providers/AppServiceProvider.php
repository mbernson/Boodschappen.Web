<?php

namespace Boodschappen\Providers;

use Illuminate\Support\ServiceProvider;

use Boodschappen\Crawling\DataSources\AlbertHeijn;
use Boodschappen\Crawling\DataSources\Jumbo;

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
        Jumbo::class,
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
    }
}
