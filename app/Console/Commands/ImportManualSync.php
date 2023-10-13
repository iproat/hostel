<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Cron;
use App\Http\Controllers\Employee\AccessController;

class ImportManualSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devicelog:importmanual';
    protected $name      = "devicelog-importmanual";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Device Log';

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
     * @return mixed
     */
    public function handle(){   
        
       // \Log::info('Import Log Cron');
        //return true;
        $cron = Cron::where('status', 0)->first();

        if($cron){      
            $cron->status=1;
            $cron->update();
        
            $controller = new AccessController();
            $controller->log(new \Illuminate\Http\Request());

            \Log::info("Cron is working fine!");

            $cron->status=2;
            $cron->update();
        }
    }
}
