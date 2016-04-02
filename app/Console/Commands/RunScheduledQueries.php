<?php

namespace Boodschappen\Console\Commands;

use Boodschappen\Jobs\QueryProductsJob;
use DB;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RunScheduledQueries extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boodschappen:queries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add jobs for scheduled product queries';

    private $db;

    /**
     * Create a new command instance.
     *
     */
    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->db->transaction(function(Connection $db) {
            $table = 'schedule';
            $interval = DB::raw("now() - interval '2 days'");
            $queries = $db->table($table)->where('last_crawled_at', '<=', $interval)->select('query')->pluck('query');

$count = count($queries);
        if($count == 0) {
$this->info('No queries available to run');
} else {
$this->info("Going to run {$count} queries");
}

            foreach($queries as $query) {
                echo "Dispatched $query\n";
                $this->dispatchSearch($query);
            }

            $db->table($table)->where('last_crawled_at', '<=', $interval)->update([
                'last_crawled_at' => DB::raw('now()')
            ]);
        });
    }


    private function dispatchSearch($query) {
        $product_sources = [
            \Boodschappen\Crawling\DataSources\Hoogvliet::class,
            \Boodschappen\Crawling\DataSources\Jumbo::class,
            \Boodschappen\Crawling\DataSources\AlbertHeijn::class,
        ];
        foreach($product_sources as $klass) {
            $job = new \Boodschappen\Jobs\QueryProductsJob($klass, $query);
            $this->dispatch($job);
        }
    }
}
