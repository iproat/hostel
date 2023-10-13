<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendanceMusterReportExport;
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
use App\Repositories\AttendanceRepository;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
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

        return view('admin.attendance.report.dailyAttendance', ['results' => $results, 'branchList' => $branchList, 'departmentList' => $departmentList, 'date' => $request->date, 'branch_id' => $request->branch_id, 'department_id' => $request->department_id, 'attendance_status' => $request->attendance_status]);
    }

    public function monthlyAttendance(Request $request)
    {
        \set_time_limit(0);

        $employeeList = Employee::get();
        // dd($interval->h, $interval->i, $totTime);
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        }
        // dd($results);
        return view('admin.attendance.report.monthlyAttendance', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
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
        $leaveType = LeaveType::get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($month);

        // dd($month);
        // dd($result);
        // dd($monthToDate);

        return view('admin.attendance.report.summaryReport', ['results' => $result, 'monthToDate' => $monthToDate, 'month' => $month, 'leaveTypes' => $leaveType, 'monthName' => $monthName]);
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

        $excel = new MonthlyAttendanceReportExport('admin.attendance.report.monthlyAttendancePagination', [
            'printHead' => $printHead, 'employeeInfo' => $employeeInfo, 'results' => $results, 'employeeList' => $employeeList,
            'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
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
}
