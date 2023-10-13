<?php

namespace App\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\View\EmployeeAttendaceController;
use Carbon\Carbon;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';
    protected $name      = "test-cron";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Cron';

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
        info(Carbon::now());

        $date = new DateTime(Carbon::now(), new DateTimeZone('Asia/Kolkata'));
        date_default_timezone_set('Asia/Muscat');
        $inouttime = date("Y-m-d h:i:s", $date->format('U'));
        info($inouttime);
    }
}
