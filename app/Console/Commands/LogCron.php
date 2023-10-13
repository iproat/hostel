<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\LeaveRepository;
use App\Repositories\AttendanceRepository;
use App\Http\Controllers\Attendance\GenerateReportController;

class LogCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:generate';
    protected $name      = "Report-generate";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run to insert raw attendance logs';

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
        Log::info("Log cron is working fine!");
        $date = date('Y-m-d');
        $leaveRepository = new LeaveRepository;
        $attendanceRepository = new AttendanceRepository;
        $controller = new GenerateReportController($leaveRepository, $attendanceRepository);
        $controller->generateAttendanceReportCron($date);

        /*
    Write your database logic we bellow:
    Item::create(['name'=>'hello new']);
     */
    }
}
