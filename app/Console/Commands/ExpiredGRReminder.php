<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExpiredGRReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:mail_expired_gr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email For Expired GR';

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
        $gr_detail = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0
                ],
                [
                    "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "type" => "ASC",
                ],
                [
                    "field" => "TR_GR_DETAIL_EXP_DATE",
                    "type" => "ASC",
                ],
                [
                    "field" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);

        $grouped_gr = array();

        foreach($gr_detail as $item)
        {
            $grouped_gr[$item['TR_GR_DETAIL_UNLOADING_PLANT']][] = $item;
        }
    
        foreach ($grouped_gr as $key => $value) {
            $mail = std_get([
                "select" => ["MA_USRACC_FULL_NAME","MA_USRACC_PLANT_CODE","MA_USRACC_EMAIL"],
                "table_name" => "MA_USRACC",
                "where" => [
                    [
                        "field_name" => "MA_USRACC_ROLE",
                        "operator" => "=",
                        "value" => 2
                    ],
                    [
                        "field_name" => "MA_USRACC_PLANT_CODE",
                        "operator" => "=",
                        "value" => $key
                    ]
                ],
                "first_row" => false
            ]);
            if ($mail != NULL) {
                $temp_mail = [
                    [
                        "MA_USRACC_EMAIL" => "96jonathansimanta@gmail.com"
                    ],
                    [
                        "MA_USRACC_EMAIL" => "96jonathansimanta@gmail.com"
                    ]
                ];

                $mail_to = [];
                foreach ($temp_mail as $mail_data) {
                    $mail_to[] = $mail_data["MA_USRACC_EMAIL"];
                }
                $data = array('gr'=>$value, "plant" => $key);
                Mail::send('mail.gr_notification', $data, function($message) use ($mail_to) {
                    $message->to($mail_to)->subject
                        ('Notifikasi Material Yang Sudah Mau Mencapai Masa Kadaluarsa');
                    $message->from('nikolas.paundralingga@sariroti.com','GI GR Scanner System');
                });
            }
        }
        $this->info('GR Reminder Log Successfully Triggred');
        return 0;
    }
}
