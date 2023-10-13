<?php

namespace App\Http\Controllers\Api;

use App\User;
use DateTime;
use App\Model\Employee;
use App\Model\LeaveType;
use Illuminate\Http\Request;
use App\Model\LeaveApplication;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    protected $controller;
    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(Controller $controller, CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->controller = $controller;
    }

    public function index(Request $request)
    {
        try {

            $employee_id = $request->employee_id;

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->controller->custom_error($validator->getMessageBag()->first());
            }

            $results = LeaveApplication::where('employee_id', $employee_id)->latest()->get();

            $leaveType = LeaveType::get();

            $array = [
                'results' => $results,
                'leaveType' => $leaveType
            ];

            return $this->controller->success("Datas Successfully Received !!!", $array);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->controller->custom_error("Something went wrong! please try again.");
        }
    }
    public function store(Request $request)
    {
        try {
            $input = Validator::make($request->all(), [
                'employee_id' => 'required',
                'leave_type_id' => 'required',
                'application_from_date' => 'required|date',
                'application_to_date' => 'required|date',
                'purpose' => 'required|string',
            ]);
            if ($input->fails()) {
                return Controller::custom_error($input->getMessageBag()->first());
            }
            $employee_data = Employee::where('employee_id', $request->employee_id)->first();
            // dd($employee_data);
            $input                          = $request->all();
            $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
            $input['application_to_date']   = dateConvertFormtoDB($request->application_to_date);
            $input['application_date']      = date('Y-m-d');
            $input['branch_id']             = $employee_data->branch_id;
            $hod = Employee::where('user_id', $employee_data->supervisor_id)->first();
            if ($hod->employee_id) {
                $head = $hod->employee_id;
            } else {
                $head = 1;
            }
            $fromDate = new DateTime($input['application_from_date']);
            $toDate = new DateTime($input['application_to_date']);

            if ($fromDate == $toDate) {
                $no_of_days = 1;
            } else {
                $interval = $fromDate->diff($toDate);
                $no_of_days = $interval->d + 1;
            }

            $input['number_of_day'] = $no_of_days;
            $applicationexist = LeaveApplication::where('application_from_date', '>=', dateConvertFormtoDB($request->application_from_date))->where('application_to_date', '<=', dateConvertFormtoDB($request->application_to_date))->where('employee_id', $request->employee_id)->first();

            if ($applicationexist) {
                return $this->controller->custom_error("Leave application exists between selected dates. Try different dates.");
                $bug = 0;
            } else {
                LeaveApplication::create($input);
                return $this->controller->custom_success("Leave application sent successfully.");
            }
        } catch (\Throwable $th) {

            return $this->controller->error();
        }
    }
}
