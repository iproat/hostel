<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AttendanceRepository;

class ReportController extends Controller
{

    protected $controller;
    protected $commonRepository;
    protected $attendanceRepository;


    public function __construct(Controller $controller, CommonRepository $commonRepository, AttendanceRepository $attendanceRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->controller = $controller;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function index(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required',
                'date' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->controller->custom_error($validator->getMessageBag()->first());
            }

            $results  = [];

            $results = DB::select("call SP_EmployeeDailyAttendance('" . $request->date . "','" . $request->employee_id . "')");

            if (count($results) > 0) {
                return $this->controller->success("Attendacne report received successfully", $results);
            } else {
                return $this->controller->custom_error("Attendance report not found");
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->controller->error();
        }
    }
}
