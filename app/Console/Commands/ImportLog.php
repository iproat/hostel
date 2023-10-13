<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Employee\AccessController;

class ImportLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devicelog:import';
    protected $name      = "devicelog-import";

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

        

        $controller = new AccessController();
        $controller->log(new \Illuminate\Http\Request());

        \Log::info("Cron is working fine!");
    }
}
