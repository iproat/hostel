<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Employee;
use App\Model\IpSetting;
use App\Lib\Enumerations\UserStatus;
use App\Model\WhiteListedIp;
use App\Model\EmployeeInOutData;
use Illuminate\Http\Request;
use App\Model\WorkShift;
use App\Model\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepository;
use App\Repositories\CommonRepository;

use App\Http\Controllers\View\EmployeeAttendaceController;
use App\Model\Branch;

class ManualAttendanceController extends Controller
{
    protected $employeeAttendaceController;
    protected $generateReportController;
    protected $attendanceRepository;
    protected $commonRepository;

    public function __construct(EmployeeAttendaceController $employeeAttendaceController, GenerateReportController $generateReportController, AttendanceRepository $attendanceRepository, CommonRepository $commonRepository)
    {
        $this->employeeAttendaceController = $employeeAttendaceController;
        $this->generateReportController = $generateReportController;
        $this->attendanceRepository = $attendanceRepository;
        $this->commonRepository = $commonRepository;
    }
    public function manualAttendance(Request $request)
    {

        $branchList = Branch::get();
        return view('admin.attendance.manualAttendance.index', ['branchList' => $branchList]);
    }

    public function filterData(Request $request)
    {
        $data           = dateConvertFormtoDB($request->get('date'));
        $branch     = $request->get('branch_id');
        $branchList = Branch::get();

        $attendanceData = Employee::select(
            'employee.finger_id',
            'employee.branch_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.mrng_in_time), \'%h:%i %p\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS mrng_in_time'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.mrng_out_time), \'%h:%i %p\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS mrng_out_time'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.eve_in_time), \'%h:%i %p\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS eve_in_time'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.eve_out_time), \'%h:%i %p\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.finger_id ) AS eve_out_time')
        )
            ->where('employee.branch_id', $branch)
            ->where('employee.status', 1)
            ->get();

        return view('admin.attendance.manualAttendance.index', ['branchList' => $branchList, 'attendanceData' => $attendanceData]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data       = dateConvertFormtoDB($request->get('date'));
            $branch     = $request->get('branch_id');

            $result = json_decode(DB::table(DB::raw("(SELECT ms_sql.*,employee.`branch_id`,  DATE_FORMAT(`ms_sql`.`datetime`,'%Y-%m-%d') AS `date` FROM `ms_sql`
                    INNER JOIN `employee` ON `employee`.`finger_id` = ms_sql.`ID`
                    WHERE branch_id = $branch) as employeeAttendance"))
                ->select('employeeAttendance.primary_id')
                ->where('employeeAttendance.date', $data)
                ->get()->toJson(), true);

            DB::table('ms_sql')->whereIn('primary_id', array_values($result))->delete();

            foreach ($request->finger_print_id as $key => $finger_print_id) {
                $emp = Employee::where('finger_id', $finger_print_id)->first();
                if (isset($request->mrng_in_time[$key]) && isset($request->mrng_out_time[$key]) && isset($request->eve_in_time[$key]) && isset($request->eve_out_time[$key])) {
                    $MrngInData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'IN',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->mrng_in_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($MrngInData);

                    $MrngOutData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'OUT',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->mrng_out_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($MrngOutData);

                    $EveInData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'IN',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->eve_in_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($EveInData);
                    $EveOutData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'OUT',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->eve_out_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($EveOutData);
                } elseif (isset($request->mrng_in_time[$key]) && isset($request->mrng_out_time[$key])) {
                    $InData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'IN',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->mrng_in_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($InData);

                    $outData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'OUT',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->mrng_out_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($outData);
                } elseif (isset($request->eve_in_time[$key]) && isset($request->eve_out_time[$key])) {
                    $InData = [
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'IN',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->eve_in_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($InData);

                    $outData = [    
                        'ID' => $emp->finger_id,
                        'employee' => $emp->employee_id,
                        'device_employee_id' => $emp->finger_id,
                        'status' => 0,
                        'type' => 'OUT',
                        'datetime'     => dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->eve_out_time[$key])),
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                    MsSql::insert($outData);
                }
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = info($e);
        }

        if ($bug == 0) {
            return redirect('manualAttendance')->with('success', 'Attendance successfully saved.');
        } else {
            return redirect('manualAttendance')->with('error', 'Something Error Found !, Please try again.');
        }
    }
    public function individualReport(Request $request)
    {
        try {
            info($request->all());
            $recompute = false;
            $manual = true;
            $results = $this->generateReportController->generateManualAttendanceReport($request->finger_print_id, date('Y-m-d', strtotime($request->in_time)), date('Y-m-d H:i:s', strtotime($request->in_time)), date('Y-m-d H:i:s', strtotime($request->out_time)), $manual, $recompute);
            echo $results ? 'success' : 'error';
        } catch (\Throwable $th) {
            //throw $th; 
            info($th);
            echo $th->getMessage();
        }
    }
   

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
