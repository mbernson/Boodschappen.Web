<?php namespace Haystack\Reporter;

use Illuminate\Support\ServiceProvider;

class HaystackServiceProvider extends ServiceProvider
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
        $this->app->singleton(ReporterInterface::class, function() {
            $reporter = new HttpReporter(config('haystack.base_url'), config('haystack.token'));
            return $reporter;
        });
    }
}
