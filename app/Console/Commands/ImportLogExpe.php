<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Employee\AccessController;

class ImportLogExpe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importlog';
    protected $name      = "importlog";

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
	return true;
        \Log::info("Cron is working fine!");
        $controller = new AccessController();
        $controller->log(new \Illuminate\Http\Request());
    }
}
