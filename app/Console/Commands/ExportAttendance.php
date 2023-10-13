<?php

namespace App\Console\Commands;

use App\Components\Common;
use App\Model\EmployeeInOutData;
use App\Model\LastRecordID;
use Illuminate\Console\Command;

class ExportAttendance extends Command
{

    protected $signature   = 'export:attendance';
    protected $description = 'Export Attendance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

	return true;

        \DB::beginTransaction();

        $last_record = LastRecordID::findorFail(1);

        EmployeeInOutData::where('employee_attendance_id', '>', $last_record->attendance_id)->orderBy('employee_attendance_id', 'ASC')->chunk(5, function ($attendance) {

            foreach ($attendance as $Data) {

                $client   = new \GuzzleHttp\Client();
                $response = $client->request('POST', Common::liveurl() . "importattendance", [
                    'form_params' => [
                        'employee_attendance_id' => $Data->employee_attendance_id,
                        'finger_print_id'        => $Data->finger_print_id,
                        'date'                   => $Data->date,
                        'in_time_from'           => $Data->in_time_from,
                        'in_time'                => $Data->in_time,
                        'out_time'               => $Data->out_time,
                        'out_time_upto'          => $Data->out_time_upto,
                        'working_time'           => $Data->working_time,
                        'working_hour'           => $Data->working_hour,
                        'in_out_time'           => trim($Data->in_out_time),
                        'status'                 => $Data->status,
                        'created_at'             => DATE('Y-m-d H:i:s',strtotime($Data->created_at)),
                        'updated_at'             => DATE('Y-m-d H:i:s',strtotime($Data->updated_at)),
                        'over_time'             => $Data->over_time,
                        'early_by'             => $Data->early_by,
                        'late_by'             => $Data->late_by,
                        'shift_name'             => $Data->shift_name,
                        'device_name'             => $Data->device_name,
                    ]
                ]);

                $Data->live_status = 1;
                $Data->save();
            }
        });

        \DB::commit();

        $last_push = EmployeeInOutData::where('live_status', 1)->orderBy('employee_attendance_id', 'DESC')->first();
        if ($last_push) {
            $last_record->attendance_id = $last_push->employee_attendance_id;
            $last_record->save();
        }

    }
}
