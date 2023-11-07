<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:sync_master_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Master Data From SAP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        sync_master_data("CC");
        sync_master_data("GL");
        sync_master_data("MAT");
        sync_master_data("MAT_UoM");
        sync_master_data("MVT");
        sync_master_data("PLANT");
        sync_master_data("SLOC");
        sync_master_data("VENDOR");
        $this->info('Sync Master Data Cron Log Successfully Triggred');
        return 0;
    }
}
