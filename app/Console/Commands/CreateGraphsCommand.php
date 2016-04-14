<?php

namespace Boodschappen\Console\Commands;

use Illuminate\Console\Command;
use Boodschappen\Database\Product;

class CreateGraphsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graphs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates RRD graphs';

    private $rrd_path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function createDatabase()
    {
        $this->rrd_path = public_path('boodschappen.rrd');

        $options = array(
            "--step", "300",            // Use a step-size of 5 minutes
            "--start", "-6 months",     // this rrd started 6 months ago
            "DS:success:ABSOLUTE:600:0:U",
            // "DS:failure:ABSOLUTE:600:0:U",
            "RRA:AVERAGE:0.5:1:288",
            "RRA:AVERAGE:0.5:12:168",
            "RRA:AVERAGE:0.5:228:365",
        );

        $ret = rrd_create($this->rrd_path, $options);
        if (! $ret) {
            $this->error("Creation error: ".rrd_error()."\n");
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDatabase();
        $dates = Product::select('created_at')->limit(1000)->get();
        $created_at = $dates[0]->created_at->getTimestamp();
        foreach($dates as $date) {
            $ret = rrd_update($this->rrd_path, ["$created_at:1"]);
            if (! $ret) {
                $this->error("RRD error: ".rrd_error()."\n");
            }
        }
    }
}
