<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use App\Http\Controllers\Employee\AccessController;

class PushLogToServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushlog:sqlserver';
    protected $name = 'pushlog-sqlserver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
	 

        Log::info("Sqlserver Data pushing cron is working fine!");
	return true;

        //$accessController = new AccessController();
     	//$accessController->push_into_sqlserver();
    }
}
