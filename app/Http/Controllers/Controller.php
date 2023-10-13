<?php

namespace App\Http\Controllers;

use App\Model\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function tableExchage()
    {
        $ms_sql = DB::table('ms_sql')->get();
        $data = [];
        foreach ($ms_sql as $key => $value) {
            // dd($value);

            EmployeeAttendance::create([
                'employee_attendance_id' => $value->primary_id,
                'finger_print_id' => $value->ID,
                'in_out_time' => $value->datetime,
                'type' => $value->type,
                'employee_id' => $value->employee,
                'device' => $value->device,
                'live_status' => $value->live_status,
                'status' => $value->status,
                'device_employee_id' => $value->device_employee_id,
            ]);

        }
    }
    public function success($message, $data)
    {
        return response()->json([
            'status' => \true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public function successdualdata($message, $data, $list)
    {
        return response()->json([
            'status' => \true,
            'message' => $message,
            'data' => $data,
            'list' => $list,
        ], 200);
    }

    public function error()
    {
        return response()->json([
            'status' => \false,
            'message' => "Something error found !, Please try again.",
        ], 200);
    }

    public function custom_error($custom_message)
    {
        return response()->json([
            'status' => \false,
            'message' => $custom_message,
        ], 200);
    }
    
    public function custom_success($custom_message)
    {
        return response()->json([
            'status' => \true,
            'message' => $custom_message,
        ], 200);
    }
}
