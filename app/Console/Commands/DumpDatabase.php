<?php

namespace Boodschappen\Console\Commands;

use Illuminate\Console\Command;

class DumpDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database structure and content';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting...');
        $this->info('Dumping structure to database/structure.sql...');
        shell_exec('pg_dump boodschappen --schema-only --no-privileges > database/structure.sql');

        $filename = "backup/".date("Y_m_d_His")."_full_dump.sql.gz";
        $this->info("Making a full backup to $filename...");
        shell_exec("pg_dump boodschappen --no-privileges | gzip > $filename");
        $this->info('Done.');
    }
}

