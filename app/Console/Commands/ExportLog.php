<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Employee\AccessController;
use App\Model\DeviceAttendanceLog;
use App\Model\LastRecordID;
use App\Components\Common;

class ExportLog extends Command{

    protected $signature = 'export:devicelog';
    protected $description = 'Export Device Log';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
	
	return true;
         
	\Log::info('Export log cron running successfully at : ' . date('d-m-Y h:i A'));

        \DB::beginTransaction();

        $last_record=LastRecordID::findorFail(1);

        DeviceAttendanceLog::where('primary_id','>',$last_record->ms_sql_id)->orderBy('primary_id','ASC')->chunk(5, function ($device_log) {

            foreach ($device_log as $logs) {

                $client   = new \GuzzleHttp\Client();
                $response = $client->request('POST', Common::liveurl()."importlogs",[
                     'form_params' =>[
                        'primary_id'=>$logs->primary_id,
                        'ID'=>$logs->ID,
                        'type'=>$logs->type,
                        'datetime'=>$logs->datetime,
                        'status'=>$logs->status,
                        'created_at'=>$logs->created_at,
                        'updated_at'=>$logs->updated_at,
                        'employee'=>$logs->employee,
                        'device'=>$logs->device,
                        'device_employee_id'=>$logs->device_employee_id,
                        'sms_log'=>$logs->sms_log,
                     ]
                ]);

                $logs->live_status=1;
                $logs->save();
            }
        });

        \DB::commit();
        
        $last_push=DeviceAttendanceLog::where('live_status',1)->orderBy('primary_id','DESC')->first();
        if($last_push){
            $last_record->ms_sql_id=$last_push->primary_id;
            $last_record->save();
        }

    }
}
