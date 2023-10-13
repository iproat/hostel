<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Model\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Model\LeavePermission;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Validator;

class LatePermissionController extends Controller
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

            $results = LeavePermission::where('employee_id', $employee_id)->latest()->get();

            return $this->controller->success("Datas Successfully Received !!!", $results);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->controller->custom_error("Something went wrong! please try again.");
        }
    }
    public function store(Request $request)
    {
        $checkpermissions = 0;
        try {
            $input = Validator::make($request->all(), [
                'employee_id' => 'required',
                'date' => 'required|date',
                'duration' => 'required',
                'from_time' => 'required',
                'to_time' => 'required',
                'purpose' => 'required|string',
            ]);
            if ($input->fails()) {
                return Controller::custom_error($input->getMessageBag()->first());
            }
            $input = $request->all();


            $employee = Employee::where('employee_id', $request->employee_id)->first();
            $input['leave_permission_date'] = dateConvertFormtoDB($input['date']);
            $input['employee_id'] = $employee->employee_id;
            $input['branch_id'] = $employee->branch_id;
            $input['permission_duration'] = date('H:i:s', strtotime($input['duration']));
            $input['from_time'] = $input['from_time'];
            $input['to_time'] = $input['to_time'];
            $input['leave_permission_purpose'] = $input['purpose'];
            $input['status'] = 1;

            $ifExists = LeavePermission::where('leave_permission_date',  $input['date'])->where('employee_id', $input['employee_id'])->first();

            $checkpermissions = $this->applyForTotalNumberOfPermissions($request->date, $employee->employee_id);
            
            if ($checkpermissions >= 5) {
                return Controller::custom_error("The late permission application is approved only up to five times in a month..");
            }

            if ($ifExists) {
                return Controller::custom_error("Late Permission  application exists between selected dates. Try different dates.");
            }

            LeavePermission::create($input);

            return Controller::custom_success("Late Permission application sent successfully.");
        } catch (\Throwable $th) {
            
            return Controller::custom_error('Something went wrong!' . $th->getMessage());
        }
    }
    public function applyForTotalNumberOfPermissions($date, $employee_id)
    {
        
        $permission_date = $date;
        $employee_id = $employee_id;
        $Year  = date("Y", strtotime($permission_date));
        $Month = (int)date("m", strtotime($permission_date));
        $checkpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $employee_id)->where('status', 2)->count();

        return $checkpermissions;
    }
}
