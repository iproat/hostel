<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendanceMusterReportExport;
use App\Exports\ExcelExportFromView;
use App\Exports\MonthlyAttendanceReportExport;
use App\Exports\MusterAttendanceReportExport;
use App\Exports\SummaryAttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use App\Model\Branch;
use App\Model\Department;
use App\Model\Employee;
// use Barryvdh\DomPDF\Facade as PDF;
use App\Model\LeaveType;
use App\Model\MsSql;
use App\Model\PrintHeadSetting;
use App\Model\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{

    protected $attendanceRepository;
    protected $commonRepository;

    public function __construct(AttendanceRepository $attendanceRepository, CommonRepository $commonRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->commonRepository = $commonRepository;
    }

    public function dailyAttendance(Request $request)
    {

        \set_time_limit(0);

        $departmentList = Department::get();
        $branchList = Branch::get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->branch_id, $request->attendance_status);
        }
        // dd($results);
        return view('admin.attendance.report.dailyAttendance', ['results' => $results, 'branchList' => $branchList, 'departmentList' => $departmentList, 'date' => $request->date, 'branch_id' => $request->branch_id, 'department_id' => $request->department_id, 'attendance_status' => $request->attendance_status]);
    }


    public function monthlyAttendance(Request $request)
    {
        \set_time_limit(0);
        $employeeList = Employee::where('finger_id', '!=', 1)->get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        }
        // dd($results);
        return view('admin.attendance.report.monthlyAttendance', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id, 'attendance_status' => $request->attendance_status]);
    }

    public function myAttendanceReport(Request $request)
    {
        \set_time_limit(0);

        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->where('employee_id', session('logged_session_data.employee_id'))->get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), session('logged_session_data.employee_id'));
        } else {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(date('Y-m-01'), date("Y-m-t", strtotime(date('Y-m-01'))), session('logged_session_data.employee_id'));
        }

        return view('admin.attendance.report.mySummaryReport', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
    }

    public function attendanceMusterReport(Request $request)
    {
        \set_time_limit(0);

        if ($request->from_date && $request->to_date) {
            // dd($request->all());
            $month_from = date('Y-m', strtotime($request->from_date));
            $month_to = date('Y-m', strtotime($request->to_date));
            $start_date = dateConvertFormtoDB($request->from_date);
            $end_date = dateConvertFormtoDB($request->to_date);
        } else {
            $month_from = date('Y-m');
            $month_to = date('Y-m');
            $start_date = $month_from . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')->where('finger_id', '!=', 1)->where('status', UserStatus::$ACTIVE)->get();
        $branchList = Branch::get();

        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);

        $month_data_from = $monthAndYearFrom[1];
        $month_data_to = $monthAndYearTo[1];
        $dateObjFrom = DateTime::createFromFormat('!m', $month_data_from);
        $dateObjTo = DateTime::createFromFormat('!m', $month_data_to);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');

        $employeeInfo = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->where('employee_id', $request->employee_id)->first();

        $monthToDate = findMonthFromToDate($start_date, $end_date);

        if ($request->from_date && $request->to_date) {
            $result = $this->attendanceRepository->findAttendanceMusterReport($start_date, $end_date, $request->employee_id, $request->department_id, $request->branch_id);
        } else {
            $result = [];
        }
        //   dd($result);
        return view('admin.attendance.report.musterReport', [
            'departmentList' => $departmentList, 'employeeInfo' => $employeeInfo, 'employeeList' => $employeeList, 'branchList' => $branchList,
            'results' => $result, 'monthToDate' => $monthToDate, 'month_from' => $month_from, 'month_to' => $month_to, 'monthNameFrom' => $monthNameFrom,
            'monthNameTo' => $monthNameTo, 'department_id' => $request->department_id, 'employee_id' => $request->employee_id, 'branch_id' => $request->branch_id,
            'from_date' => $request->from_date, 'to_date' => $request->to_date, 'monthAndYearFrom' => $monthAndYearFrom, 'monthAndYearTo' => $monthAndYearTo,
            'start_date' => $start_date, 'end_date' => $end_date,
        ]);
    }

    public function attendanceSummaryReport(Request $request)
    {
        \set_time_limit(0);
        if ($request->month) {
            $month = $request->month;
        } else {
            $month = date("Y-m");
        }

        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $monthToDate = findMonthToAllDate($month);
        // $leaveType = LeaveType::get();
        // dd($month);

        $result = $this->attendanceRepository->findAttendanceSummaryReport($month);


        // dd($result);
        return view('admin.attendance.report.summaryReport', ['results' => $result, 'monthToDate' => $monthToDate, 'month' => $month,  'monthName' => $monthName]);
    }

    public function monthlyExcel(Request $request)
    {
        \set_time_limit(0);

        $employeeList = Employee::get();
        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead = PrintHeadSetting::first();
        $results = [];

        if ($request->from_date && $request->to_date && $request->employee_id) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        }
        // dd($results);
        $excel = new MonthlyAttendanceReportExport('admin.attendance.report.monthlyAttendancePagination', [
            'printHead' => $printHead, 'employeeInfo' => $employeeInfo, 'results' => $results, 'employeeList' => $employeeList,
            'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name, 'attendance_status' => $request->attendance_status
        ]);

        $excelFile = Excel::download($excel, 'monthlyReport' . date('Ym', strtotime($request->month)) . date('His') . '.xlsx');

        return $excelFile;
    }
    public function summaryExcel(Request $request)
    {
        \set_time_limit(0);

        $monthToDate = findMonthToAllDate($request->month);
        $leaveType = LeaveType::get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($request->month);
        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $monthAndYear = explode('-', $request->month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $data = [
            'results' => $result,
            'month' => $request->month,
            'monthToDate' => $monthToDate,
            'leaveTypes' => $leaveType,
            'monthName' => $monthName,
        ];

        $excel = new SummaryAttendanceReportExport('admin.attendance.report.summaryReportPagination', $data);

        $excelFile = Excel::download($excel, 'summaryReport' . date('Ym', strtotime($request->month)) . date('His') . '.xlsx');

        return $excelFile;
    }


    public function dailyExcel(Request $request)
    {
        \set_time_limit(0);

        $departmentList = Department::where('department_id', $request->department_id)->first();
        $branchList = Branch::where('branch_id', $request->branch_id)->first();
        $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->branch_id, $request->attendance_status);

        $data = [
            'results' => $results,
            'date' => $request->date,
            'attendance_status' => $request->attendance_status,
            'branch_id' => isset($branchList->branch_id) ? $branchList->branch_id : '',
            'branch_name' => isset($branchList->branch_name) ? $branchList->branch_name : '',
            'department_id' => isset($departmentList->department_id) ? $departmentList->department_id : '',
            'department_name' => isset($departmentList->department_name) ? $departmentList->department_name : '',

        ];

        $excel = new SummaryAttendanceReportExport('admin.attendance.report.dailyReportPagination', $data);

        $excelFile = Excel::download($excel, 'dailyReport' . date('Ymd', strtotime($request->date)) . date('His') . '.xlsx');

        return $excelFile;
    }

    public function attendanceRecord(Request $request)
    {

        $results = [];
        $results = MsSql::orderByDesc('datetime')->limit(1000)->get();

        $employeeList = Employee::where('supervisor_id', session('logged_session_data.employee_id'))->orwhere('employee_id', session('logged_session_data.employee_id'))->get();

        if (session('logged_session_data.role_id') == 1 || session('logged_session_data.role_id') == 2) {
            $employeeList = Employee::get();
        }

        if ($_POST) {

            if ($request->device_name && $request->date) {

                $results = MsSql::whereDate('datetime', Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'))
                    ->where('device_name', $request->device_name)
                    ->orderByDesc('datetime')->limit(1000)->get();
            }
        }

        return \view('admin.attendance.report.attendanceRecord', ['results' => $results, 'device_name' => $request->device_name, 'employeeList' => $employeeList, 'date' => $request->date, 'employee_id ' => $request->employee_id]);
    }
    //   public function attendanceRecord(Request $request)
    // {
    //     \set_time_limit(0);

    //     $results = [];

    //     if ($_POST) {

    //         $fdate = Carbon::createFromFormat('d/m/Y', $request->fdate)->format('Y-m-d 00:00:01');
    //         $tdate = Carbon::createFromFormat('d/m/Y', $request->tdate)->format('Y-m-d 23:59:59');

    //         if ($request->fdate && $request->tdate) {
    //             $qry = 'ms_sql.datetime >= "' . $fdate . '" and ms_sql.datetime <= "' . $tdate . '"';
    //         }

    //         $results = DB::table('ms_sql')->leftjoin('employee', 'employee.finger_id', '=', 'ms_sql.ID')->whereRaw($qry)
    //             ->select('ms_sql.datetime', 'ms_sql.ID', 'ms_sql.updated_at', DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'))->get();

    //     }
    //     return \view('admin.attendance.report.attendanceRecord', ['results' => $results, 'device_name' => $request->device_name, 'fdate' => $request->fdate, 'tdate' => $request->tdate, 'employee_id ' => $request->employee_id]);
    // }
    public function musterExcelExportFromCollection(Request $request)
    {
        //  dd(123);
        if ($request->from_date && $request->to_date) {
            // dd($request->all());
            $month_from = date('Y-m', strtotime($request->from_date));
            $month_to = date('Y-m', strtotime($request->to_date));
            $start_date = dateConvertFormtoDB($request->from_date);
            $end_date = dateConvertFormtoDB($request->to_date);
        } else {
            $month_from = date('Y-m');
            $month_to = date('Y-m');
            $start_date = $month_from . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->get();
        $branchList = Branch::get();

        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);

        $month_data_from = $monthAndYearFrom[1];
        $month_data_to = $monthAndYearTo[1];
        $dateObjFrom = DateTime::createFromFormat('!m', $month_data_from);
        $dateObjTo = DateTime::createFromFormat('!m', $month_data_to);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');

        $employeeInfo = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->where('employee_id', $request->employee_id)->first();

        $monthToDate = findMonthFromToDate($start_date, $end_date);

        if ($request->from_date && $request->to_date) {
            $result = $this->attendanceRepository->findAttendanceMusterReport($start_date, $end_date, $request->employee_id, $request->department_id, $request->branch_id);
        } else {
            $result = [];
        }
        $data = [
            'departmentList' => $departmentList, 'employeeInfo' => $employeeInfo, 'employeeList' => $employeeList, 'branchList' => $branchList,
            'results' => $result, 'monthToDate' => $monthToDate, 'month_from' => $month_from, 'month_to' => $month_to, 'monthNameFrom' => $monthNameFrom,
            'monthNameTo' => $monthNameTo, 'department_id' => $request->department_id, 'employee_id' => $request->employee_id, 'branch_id' => $request->branch_id,
            'from_date' => $request->from_date, 'to_date' => $request->to_date, 'monthAndYearFrom' => $monthAndYearFrom, 'monthAndYearTo' => $monthAndYearTo,
            'start_date' => $start_date, 'end_date' => $end_date,
        ];

        $excel = new MusterAttendanceReportExport('admin.attendance.report.musterReportPagination', $data);

        $excelFile = Excel::download($excel, 'summaryReport' . date('Ym', strtotime($request->month)) . date('His') . '.xlsx');

        return $excelFile;
        // return view('admin.attendance.report.musterReportPagination', [
        //     'departmentList' => $departmentList, 'employeeInfo' => $employeeInfo, 'employeeList' => $employeeList, 'branchList' => $branchList,
        //     'results' => $result, 'monthToDate' => $monthToDate, 'month_from' => $month_from, 'month_to' => $month_to, 'monthNameFrom' => $monthNameFrom,
        //     'monthNameTo' => $monthNameTo, 'department_id' => $request->department_id, 'employee_id' => $request->employee_id, 'branch_id' => $request->branch_id,
        //     'from_date' => $request->from_date, 'to_date' => $request->to_date, 'monthAndYearFrom' => $monthAndYearFrom, 'monthAndYearTo' => $monthAndYearTo,
        //     'start_date' => $start_date, 'end_date' => $end_date,
        // ]);
        // return Excel::download(new AttendanceMusterReportExport($dataset, $extraData), 'summaryReport' . date('Ymd', strtotime($request->date)) . date('His') . '.xlsx');
    }

    public function musterReportExcelFormat()
    {
        $extraData = [];
        $monthToDate = findMonthFromToDate('2023-01-01', '2023-01-10');
        $inner_head = ['Sr.No.', 'Branch', 'Emp. ID', 'Student Name', 'Name', 'Department', 'In/Out/Shift'];
        foreach ($monthToDate as $Day) {
            $inner_head[] = $Day['day'];
        }

        $heading = [
            [
                'Attendance Summary Report',
            ],
            $inner_head,
        ];
        $extraData = ['heading' => $heading];

        $dataset = $this->attendanceRepository->findAttendanceMusterReport('2023-01-01', '2023-01-10', 'ADM1001', 2, 1);

        return Excel::download(new AttendanceMusterReportExport($dataset, $extraData), 'summaryReport' . date('Ymd', strtotime('2023-01-01')) . date('His') . '.xlsx');
    }

    public function getEmployeeLeaveRecord($from_date, $to_date, $employee_id)
    {
        $queryResult = LeaveApplication::select('application_from_date', 'application_to_date')
            ->where('status', LeaveStatus::$APPROVE)
            ->where('application_from_date', '>=', $from_date)
            ->where('application_to_date', '<=', $to_date)
            ->where('employee_id', $employee_id)
            ->get();
        $leaveRecord = [];
        foreach ($queryResult as $value) {
            $start_date = $value->application_from_date;
            $end_date = $value->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leaveRecord[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }
        return $leaveRecord;
    }

    public function getEmployeeHolidayRecord($from_date, $to_date, $employee_id)
    {
        $queryResult = WeeklyHoliday::select('weekoff_days')
            ->where('employee_id', $employee_id)
            ->where('month', date('Y-m', strtotime($from_date)))
            ->orWhere('month', date('Y-m', strtotime($to_date)))
            ->first();

        $holidayRecord = [];
        if ($queryResult) {
            foreach (\json_decode($queryResult['weekoff_days']) as $value) {
                $holidayRecord[] = $value;
            }
        }
        return $holidayRecord;
    }
    // public function findAttendanceMusterReport($start_date, $end_date, $employee_id = '', $department_id = '', $branch_id = '')
    // {
    //     $data = findMonthFromToDate($start_date, $end_date);

    //     $qry = '1 ';

    //     if ($employee_id != '') {
    //         $qry .= ' AND employee.employee_id=' . $employee_id;
    //     }
    //     if ($department_id != '') {
    //         $qry .= ' AND employee.department_id=' . $department_id;
    //     }
    //     if ($branch_id != '') {
    //         $qry .= ' AND employee.branch_id=' . $branch_id;
    //     }

    //     $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'branch_name', 'finger_id', 'employee_id')
    //         ->join('designation', 'designation.designation_id', 'employee.designation_id')
    //         ->join('department', 'department.department_id', 'employee.department_id')
    //         ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('branch.branch_name', 'ASC')->whereRaw($qry)
    //         ->where('status', UserStatus::$ACTIVE)->get();

    //     $attendance = DB::table('view_employee_in_out_data')->groupBy('date', 'finger_print_id')->orderBy('created_at', 'ASC')->whereBetween('date', [$start_date, $end_date])->get();

    //     $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));

    //     $dataFormat = [];
    //     $tempArray = [];

    //     foreach ($employees as $employee) {

    //         foreach ($data as $key => $value) {

    //             $tempArray['employee_id'] = $employee->employee_id;
    //             $tempArray['finger_id'] = $employee->finger_id;
    //             $tempArray['fullName'] = $employee->fullName;
    //             $tempArray['designation_name'] = $employee->designation_name;
    //             $tempArray['department_name'] = $employee->department_name;
    //             $tempArray['branch_name'] = $employee->branch_name;
    //             $tempArray['date'] = $value['date'];
    //             $tempArray['day'] = $value['day'];
    //             $tempArray['day_name'] = $value['day_name'];

    //             $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);

    //             $ifPublicHoliday = $this->ifPublicHoliday($govtHolidays, $value['date']);

    //             if ($ifPublicHoliday) {
    //                 $tempArray['attendance_status'] = 'holiday';
    //                 $tempArray['shift_name'] = $hasAttendance['shift_name'];
    //                 $tempArray['in_time'] = $hasAttendance['in_time'];
    //                 $tempArray['out_time'] = $hasAttendance['out_time'];
    //                 $tempArray['working_time'] = $hasAttendance['working_time'];
    //                 $tempArray['over_time'] = $hasAttendance['over_time'];
    //                 $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
    //                 $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
    //             } elseif ($hasAttendance) {
    //                 $tempArray['attendance_status'] = 'present';
    //                 $tempArray['shift_name'] = $hasAttendance['shift_name'];
    //                 $tempArray['in_time'] = $hasAttendance['in_time'];
    //                 $tempArray['out_time'] = $hasAttendance['out_time'];
    //                 $tempArray['working_time'] = $hasAttendance['working_time'];
    //                 $tempArray['over_time'] = $hasAttendance['over_time'];
    //                 $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
    //                 $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
    //             } else {

    //                 $tempArray['attendance_status'] = 'absence';
    //                 $tempArray['shift_name'] = '';
    //                 $tempArray['in_time'] = '';
    //                 $tempArray['out_time'] = '';
    //                 $tempArray['over_time'] = '';
    //                 $tempArray['working_time'] = '';
    //                 $tempArray['over_time_status'] = '';
    //                 $tempArray['employee_attendance_id'] = '';
    //             }

    //             $dataFormat[$employee->finger_id][] = $tempArray;
    //         }
    //     }

    //     return $dataFormat;
    // }
    public function findAttendanceMusterReport($start_date, $end_date, $employee_id = '', $department_id = '', $branch_id = '')
    {
        $data = findMonthFromToDate($start_date, $end_date);

        $qry = '1 ';

        if ($employee_id != '') {
            $qry .= ' AND employee.employee_id=' . $employee_id;
        }
        if ($department_id != '') {
            $qry .= ' AND employee.department_id=' . $department_id;
        }
        if ($branch_id != '') {
            $qry .= ' AND employee.branch_id=' . $branch_id;
        }

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'branch_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('branch.branch_name', 'ASC')->whereRaw($qry)
            ->where('status', UserStatus::$ACTIVE)->get();

        $attendance = DB::table('view_employee_in_out_data')->groupBy('date', 'finger_print_id')->orderBy('created_at', 'ASC')->whereBetween('date', [$start_date, $end_date])->get();

        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            foreach ($data as $key => $value) {

                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);

                $ifPublicHoliday = $this->ifPublicHoliday($govtHolidays, $value['date']);

                if ($ifPublicHoliday) {
                    $tempArray['attendance_status'] = 'holiday';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                } elseif ($hasAttendance) {
                    $tempArray['attendance_status'] = 'present';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                } else {

                    $tempArray['attendance_status'] = 'absence';
                    $tempArray['shift_name'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['over_time'] = '';
                    $tempArray['working_time'] = '';
                    $tempArray['over_time_status'] = '';
                    $tempArray['employee_attendance_id'] = '';
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        return $dataFormat;
    }

    public function findAttendanceMusterReportExcelDump($start_date, $end_date, $employee_id, $department_id, $branch_id)
    {
        $data = findMonthFromToDate($start_date, $end_date);

        $qry = '1 ';

        if ($employee_id != '') {
            $qry .= ' AND employee.employee_id=' . $employee_id;
        }
        if ($department_id != '') {
            $qry .= ' AND employee.department_id=' . $department_id;
        }
        if ($branch_id != '') {
            $qry .= ' AND employee.branch_id=' . $branch_id;
        }

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'branch_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('branch.branch_name', 'ASC')->whereRaw($qry)
            ->where('status', UserStatus::$ACTIVE)->get();

        $attendance = DB::table('view_employee_in_out_data')->groupBy('date', 'finger_print_id')->orderBy('created_at', 'ASC')->whereBetween('date', [$start_date, $end_date])->get();

        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            foreach ($data as $key => $value) {

                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);
                if ($hasAttendance) {

                    $ifHoliday = $this->ifHoliday($govtHolidays, $value['date'], $employee->employee_id);
                    // dump($ifHoliday);
                    if ($ifHoliday['govt_holiday'] == true) {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['shift_name'] = $hasAttendance['shift_name'];
                        $tempArray['in_time'] = $hasAttendance['in_time'];
                        $tempArray['out_time'] = $hasAttendance['out_time'];
                        $tempArray['working_time'] = $hasAttendance['working_time'];
                        $tempArray['over_time'] = $hasAttendance['over_time'];
                        $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
                        $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    } else {
                        $tempArray['attendance_status'] = 'holiday';
                        $tempArray['shift_name'] = $hasAttendance['shift_name'];
                        $tempArray['in_time'] = $hasAttendance['in_time'];
                        $tempArray['out_time'] = $hasAttendance['out_time'];
                        $tempArray['working_time'] = $hasAttendance['working_time'];
                        $tempArray['over_time'] = $hasAttendance['over_time'];
                        $tempArray['over_time_status'] = $hasAttendance['over_time_status'];
                        $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    }
                } else {

                    $tempArray['attendance_status'] = 'absence';
                    $tempArray['shift_name'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['over_time'] = '';
                    $tempArray['working_time'] = '';
                    $tempArray['over_time_status'] = '';
                    $tempArray['employee_attendance_id'] = '';
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        $excelFormat = [];
        $days = [];
        $sl = 1;
        $dataset = [];

        $sl = 0;
        $emptyArr = ['', '', '', '', ''];

        foreach ($dataFormat as $key => $data) {
            $sl++;

            $shiftInfo = ['SHIFT NAME'];
            $inTimeInfo = ['IN TIME'];
            $outTimeInfo = ['OUT TIME'];
            $workingTimeInfo = ['WORKING TIME'];
            $overTimeInfo = ['OVER TIME'];

            for ($i = 0; $i < count($data); $i++) {
                $employeeData = [$sl, $data[0]['branch_name'], $data[0]['finger_id'], $data[0]['fullName'], $data[0]['department_name']];
                $shiftInfo[] = $data[$i]['shift_name'] != null ? $data[$i]['shift_name'] : 'NA';
                $inTimeInfo[] = $data[$i]['in_time'] != null ? date('H:i', strtotime($data[$i]['in_time'])) : '00:00';
                $outTimeInfo[] = $data[$i]['out_time'] != null ? date('H:i', strtotime($data[$i]['out_time'])) : '00:00';
                $workingTimeInfo[] = $data[$i]['working_time'] != null ? date('H:i', strtotime($data[$i]['working_time'])) : '00:00';
                $overTimeInfo[] = $data[$i]['over_time'] != null ? date('H:i', strtotime($data[$i]['over_time'])) : '00:00';
            }

            $excelFormat[] = array_merge($employeeData, $shiftInfo);
            $excelFormat[] = array_merge($emptyArr, $inTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $outTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $workingTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $overTimeInfo);
        }
        // dump($excelFormat);
        return $excelFormat;
    }

    public function hasEmployeeMusterAttendance($attendance, $finger_print_id, $date)
    {
        $dataFormat = [];
        $dataFormat['in_time'] = '';
        $dataFormat['out_time'] = '';
        $dataFormat['over_time'] = '';
        $dataFormat['working_time'] = '';
        $dataFormat['over_time_status'] = '';
        $dataFormat['shift_name'] = '';
        $dataFormat['employee_attendance_id'] = '';

        foreach ($attendance as $key => $val) {
            // dd($val);
            if (($val->finger_print_id == $finger_print_id && $val->date == $date && $val->in_time != null)) {
                $dataFormat['shift_name'] = $val->shift_name;
                $dataFormat['in_time'] = $val->in_time;
                $dataFormat['out_time'] = $val->out_time;
                $dataFormat['over_time'] = $val->over_time;
                $dataFormat['working_time'] = $val->working_time;
                $dataFormat['over_time_status'] = $val->over_time_status;
                $dataFormat['employee_attendance_id'] = $val->employee_attendance_id;
                return $dataFormat;
            }
        }
        return $dataFormat;
    }

    public function findAttendanceSummaryReport($start_date, $end_date, $branch_id = '')
    {
        $data = findMonthFromToDate($start_date, $end_date);
        $qry = '1 ';
        if ($branch_id != '') {
            $qry .= ' AND employee.branch_id=' . $branch_id;
        }
        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'department_name', 'branch_name', 'designation_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->whereRaw($qry)
            ->orderBy('branch.branch_name', 'ASC')
            ->where('status', UserStatus::$ACTIVE)->get();

        $attendance = DB::table('view_employee_in_out_data')->select('finger_print_id', 'date', 'in_time', 'out_time', 'working_time')->groupBy('date', 'finger_print_id')->orderBy('created_at', 'ASC')->whereBetween('date', [$start_date, $end_date])->get();

        $leave = LeaveApplication::select('application_from_date', 'application_to_date', 'employee_id', 'leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
            ->whereRaw("application_from_date >= '" . $start_date . "' and application_to_date <=  '" . $end_date . "'")
            ->where('status', LeaveStatus::$APPROVE)->get();

        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            foreach ($data as $key => $value) {

                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeAttendance($attendance, $employee->finger_id, $value['date']);

                if ($hasAttendance['status']) {
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $ifHoliday = $this->ifHoliday($govtHolidays, $value['date'], $employee->employee_id);

                    if ($ifHoliday['weekly_holiday'] == true) {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['gov_day_worked'] = 'no';
                        $tempArray['leave_type'] = '';
                    } elseif ($ifHoliday['govt_holiday'] == true) {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['gov_day_worked'] = 'yes';
                        $tempArray['leave_type'] = '';
                    } else {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['leave_type'] = '';
                        $tempArray['gov_day_worked'] = 'no';
                    }
                } else {

                    if ($value['date'] > date("Y-m-d")) {

                        $tempArray['attendance_status'] = '';
                        $tempArray['gov_day_worked'] = 'no';
                        $tempArray['leave_type'] = '';
                    } else {

                        $ifHoliday = $this->ifHoliday($govtHolidays, $value['date'], $employee->employee_id);

                        if ($ifHoliday['weekly_holiday'] == true) {

                            $tempArray['attendance_status'] = 'holiday';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                        } elseif ($ifHoliday['govt_holiday'] == true) {

                            $tempArray['attendance_status'] = 'holiday';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                        } else {

                            $tempArray['attendance_status'] = 'absence';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                        }
                    }
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }
        return $dataFormat;
    }

    public function hasEmployeeAttendance($attendance, $finger_print_id, $date)
    {
        $temp = [];
        $temp['status'] = false;
        $temp['working_time'] = null;
        foreach ($attendance as $key => $val) {
            if (($val->finger_print_id == $finger_print_id && $val->date == $date && $val->in_time != null)) {
                $temp['status'] = true;
                $temp['working_time'] = $val->working_time;
            }
        }
        return $temp;
    }

    public function ifEmployeeWasLeave($leave, $employee_id, $date)
    {
        $leaveRecord = [];
        $temp = [];
        foreach ($leave as $value) {
            if ($employee_id == $value->employee_id) {
                $start_date = $value->application_from_date;
                $end_date = $value->application_to_date;
                while (strtotime($start_date) <= strtotime($end_date)) {
                    $temp['employee_id'] = $employee_id;
                    $temp['date'] = $start_date;
                    $temp['leave_type_name'] = $value->leave_type_name;
                    $leaveRecord[] = $temp;
                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                }
            }
        }

        foreach ($leaveRecord as $val) {

            if (($val['employee_id'] == $employee_id && $val['date'] == $date)) {
                return $val['leave_type_name'];
            }
        }

        return false;
    }

    public function ifPublicHoliday($govtHolidays, $date)
    {
        $govt_holidays = [];

        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }
        return false;
    }

    public function ifHoliday($govtHolidays, $date, $employee_id)
    {

        $govt_holidays = [];
        $result = [];
        $result['govt_holiday'] = false;
        $result['weekly_holiday'] = false;

        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                $result['govt_holiday'] = true;
            }
        }

        return $result;
    }
}
