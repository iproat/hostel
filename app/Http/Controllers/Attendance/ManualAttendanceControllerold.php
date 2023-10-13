<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Employee;
use App\Model\IpSetting;
use App\Model\WhiteListedIp;
use Illuminate\Http\Request;
use App\Model\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\View\EmployeeAttendaceController;

class ManualAttendanceController extends Controller
{
    protected $employeeAttendaceController;

    public function __construct(EmployeeAttendaceController $employeeAttendaceController)
    {
        $this->employeeAttendaceController = $employeeAttendaceController;
    }

    public function manualAttendance()
    {
        $employeeList = Employee::orderBy('finger_id', 'ASC')->get();
        return view('admin.attendance.manualAttendance.index', ['employeeList' => $employeeList]);
    }

    public function filterData(Request $request)
    {
        $data           = dateConvertFormtoDB($request->get('date'));
        $employee     = $request->get('employee_id');
        $employeeList = Employee::get();

        $employeeList = Employee::where('supervisor_id', session('logged_session_data.employee_id'))->orwhere('employee_id', session('logged_session_data.employee_id'))->get();

        if (session('logged_session_data.role_id') == 1 || session('logged_session_data.role_id') == 2) {
            $employeeList = Employee::get();
        }

        $attendanceData = Employee::select(
            'employee.finger_id',
            'employee.employee_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.in_time), \'%Y-%m-%d %H:%i:%s\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS inTime'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.out_time), \'%Y-%m-%d %H:%i:%s\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS outTime'),
            DB::raw('(SELECT view_employee_in_out_data.created_by FROM view_employee_in_out_data WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id) AS createdBy'),
            DB::raw('(SELECT view_employee_in_out_data.updated_by FROM view_employee_in_out_data WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id) AS updatedBy'),
            DB::raw('(SELECT view_employee_in_out_data.created_at FROM view_employee_in_out_data WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id) AS createdAt'),
            DB::raw('(SELECT view_employee_in_out_data.updated_at FROM view_employee_in_out_data WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id) AS updatedAt'),
        )
            ->where('employee.employee_id', $employee)
            ->where('employee.status', 1)
            ->get();

        // dd($attendanceData);
        return view('admin.attendance.manualAttendance.index', ['employeeList' => $employeeList, 'attendanceData' => $attendanceData]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {

            DB::beginTransaction();
            $data       = dateConvertFormtoDB($request->get('date'));
            $employee = $request->get('employee_id');

            $result = json_decode(DB::table(DB::raw("(SELECT ms_sql.*,employee.`employee_id`,  DATE_FORMAT(`ms_sql`.`datetime`,'%Y-%m-%d') AS `date` FROM `ms_sql`
                    INNER JOIN `employee` ON `employee`.`finger_id` = ms_sql.`ID`
                    WHERE employee_id = $employee) as employeeAttendance"))
                ->select('employeeAttendance.primary_id')
                ->where('employeeAttendance.date', $data)
                ->get()->toJson(), true);

            $delete =  DB::table('ms_sql')->whereIn('primary_id', array_values($result))->delete();

            foreach ($request->finger_print_id as $key => $finger_print_id) {
                if (isset($request->inTime[$key]) && isset($request->outTime[$key])) {

                    $InData = [
                        'ID' => $finger_print_id,
                        'type' => 'IN',
                        'status' => 0,
                        'device_name' => 'Manual',
                        'devuid' => 'Manual',
                        'employee'=>$employee,
                        'device_employee_id'=>$finger_print_id,
                        'datetime'     =>  date("Y-m-d H:i:s", strtotime($request->inTime[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                        'updated_by'      => auth()->user()->user_id,
                        'created_by'      => auth()->user()->user_id,
                    ];

                    MsSql::insert($InData);

                    $outData = [
                        'ID' => $finger_print_id,
                        'type' => 'OUT',
                        'status' => 0,
                        'device_name' => 'Manual',
                        'devuid' => 'Manual',
                        'employee'=>$employee,
                        'device_employee_id'=>$finger_print_id,
                        'datetime'     =>  date("Y-m-d H:i:s", strtotime($request->outTime[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                        'updated_by'      => auth()->user()->user_id,
                        'created_by'      => auth()->user()->user_id,
                    ];

                    MsSql::insert($outData);

                    $this->employeeAttendaceController->attendance($finger_print_id, true, dateConvertFormtoDB($request->date));
                } elseif (isset($request->inTime[$key])) {
                    $InData = [
                        'ID' => $finger_print_id,
                        'type' => 'IN',
                        'status' => 0,
                        'device_name' => 'Manual',
                        'devuid' => 'Manual',
                        'employee'=>$employee,
                        'device_employee_id'=>$finger_print_id,
                        'datetime'     => date("Y-m-d H:i:s", strtotime($request->inTime[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                        'updated_by'      => auth()->user()->user_id,
                        'created_by'      => auth()->user()->user_id,
                    ];

                    MsSql::insert($InData);

                    $this->employeeAttendaceController->attendance($finger_print_id, true, dateConvertFormtoDB($request->date));
                }
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            dd($e);
        }

        if ($bug == 0) {
            return redirect('manualAttendance')->with('success', 'Attendance successfully saved.');
        } else {
            return redirect('manualAttendance')->with('error', 'Something Error Found !, Please try again. ' . $bug);
        }
    }

    // ip attendance

    public function ipAttendance(Request $request)
    {

        try {

            $finger_id       = $request->finger_id;
            $ip_check_status = $request->ip_check_status;
            $user_ip         = \Request::ip();

            if ($ip_check_status == 0) {
                $att                  = new EmployeeAttendance;
                $att->finger_print_id = $finger_id;
                $att->in_out_time     = date("Y-m-d H:i:s");
                $att->save();

                return redirect()->back()->with('success', 'Attendance updated.');
            } else {
                $check_white_listed = WhiteListedIp::where('white_listed_ip', '=', $user_ip)->count();

                if ($check_white_listed > 0) {

                    $att                  = new EmployeeAttendance;
                    $att->finger_print_id = $finger_id;
                    $att->in_out_time     = date("Y-m-d H:i:s");
                    $att->save();

                    return redirect()->back()->with('success', 'Attendance updated.');
                } else {
                    return redirect()->back()->with('error', 'Invalid Ip Address.');
                }
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    // get to attendance ip setting page

    public function setupDashboardAttendance()
    {
        $ip_setting      = IpSetting::orderBy('updated_at', 'desc')->first();
        $white_listed_ip = WhiteListedIp::all();

        return view('admin.attendance.setting.dashboard_attendance', [
            'ip_setting'      => $ip_setting,
            'white_listed_ip' => $white_listed_ip,
        ]);
    }

    // post new attendance

    public function postDashboardAttendance(Request $request)
    {

        try {

            DB::beginTransaction();

            $setting = IpSetting::orderBy('id', 'desc')->first();

            $setting->status    = $request->status;
            $setting->ip_status = $request->ip_status;
            $setting->update();

            if ($request->ip) {

                WhiteListedIp::orderBy('id', 'desc')->delete();
                foreach ($request->ip as $value) {

                    if ($value != '') {

                        $white_listed_ip = new WhiteListedIp;

                        $white_listed_ip->white_listed_ip = $value;

                        $white_listed_ip->save();
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Employee Attendance Setting Updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
