<?php

namespace Boodschappen\Console\Commands;

use Boodschappen\Jobs\QueryProductsJob;
use Illuminate\Console\Command;

class QueryProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query products and add them to the database';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $product_sources = config('boodschappen.product_sources');
        $query = $this->argument('query');
        foreach($product_sources as $klass) {
            echo("Manually adding query to $klass for '$query'...\n");
            $job = new QueryProductsJob($klass, $query);
            $job->handle();
        }
        return true;
    }
}
