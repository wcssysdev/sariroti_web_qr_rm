<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveLockedGR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:remove_locked_GR';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Locked GR Data';

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
        $material_data = std_get([
            "select" => ["TR_GR_DETAIL_LOCK_ID"],
            "table_name" => "TR_GR_DETAIL_LOCK",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_LOCK_EXPIRED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => date("Y-m-d H:i:s")
                ]
            ]
        ]);

        foreach ($material_data as $row) {
            DB::table('TR_GR_DETAIL_LOCK')->where('TR_GR_DETAIL_LOCK_ID', $row["TR_GR_DETAIL_LOCK_ID"])->delete();
        }
        $this->info('GR Detail Lock Cron Successfully Triggered');
        return 0;
    }
}
