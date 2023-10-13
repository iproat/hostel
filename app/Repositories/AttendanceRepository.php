<?php

namespace App\Repositories;

use App\Model\Device;
use App\Model\Employee;
use App\Model\WorkShift;
use App\Model\WeeklyHoliday;
use App\Model\LeavePermission;
use App\Model\LeaveApplication;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\UserStatus;
use App\Lib\Enumerations\LeaveStatus;
use App\Model\MsSql;

class AttendanceRepository
{
    public function   getEmployeeDailyAttendance($date = false, $department_id, $branch_id, $status)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        // if ($status == 2) {
        //     $queryResults =  DB::select("call `SP_DailyAttendance`('" . $data . "','" . $status . "')");
        // }
        $queryResults =  DB::select("call `SP_DailyAttendance`('" . $data . "')");
        // $queryResults = DB::select("call `SP_DepartmentDailyAttendance`('" . $data . "', '" . $department_id . "','" . $branch_id . "','" . $status . "')");
        // dd($queryResults);
        // $results = [];
        // foreach ($queryResults as $value) {
        //     $results[$value->department_name][] = $value;
        //     $value->first_branch = Device::where('id', $value->first_device)->select('name')->first();
        //     $value->second_branch = Device::where('id', $value->second_device)->select('name')->first();
        //     $permission = LeavePermission::where('employee_id', $value->employee_id)->where('status', 2)->where('leave_permission_date', $value->date)->first();
        //     $leave = LeaveApplication::where('employee_id', $value->employee_id)->where('application_from_date', '<=', $value->date)
        //         ->where('application_to_date', '>=', $value->date)->where('status', 2)->first();
        //     if ($permission != '') {
        //         $permission_exist = 1;
        //         $permission_hour = $permission->permission_duration;
        //     } else {
        //         $permission_exist = 0;
        //         $permission_hour = '00:00';
        //     }
        //     if ($leave != '') {
        //         $leave_exist = 1;
        //     } else {
        //         $leave_exist = 0;
        //     }
        //     $value->permission_exist = $permission_exist;
        //     $value->permission_hour =  $permission_hour;
        //     $value->leave_exist     =  $leave_exist;
        // }
        return $queryResults;
    }


    // public function  getEmployeeDailyAttendance($date = false, $shift, $status)
    // {
    //     if ($date) {
    //         $data = dateConvertFormtoDB($date);
    //     } else {
    //         $data = date("Y-m-d");
    //     }

    //     $result = [];
    //     if ($shift) {
    //         $work_shift = WorkShift::where('work_shift_id', $shift)->first();
    //         $minTime = date('Y-m-d H:i:s',  strtotime($work_shift['start_time']));
    //         $maxTime = date('Y-m-d H:i:s',  strtotime($work_shift['end_time']));
    //         $workShiftStartTime = DATE('Y-m-d', strtotime($data)) . " " . date('H:i:s', strtotime($minTime));
    //         $workShiftEndTime = DATE('Y-m-d', strtotime($data)) . " " . date('H:i:s', strtotime($maxTime));


    //         $result =  MsSql::with('Employee')
    //             ->whereBetween('datetime', [$workShiftStartTime, $workShiftEndTime])
    //             ->select('ID', 'employee', DB::raw('MIN(datetime) as datetime'))->groupBy('ID')->get();
    //     } else {
    //         $result =  DB::select("call `SP_DailyAttendance`('" . $data . "')");
    //     }
    //     // dd($result);
    //     return $result;
    // }

    public function getEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {
        $monthlyAttendanceData = DB::select("CALL `SP_monthlyAttendance`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
        // dd($monthlyAttendanceData);
        $workingDates = $this->number_of_working_days_date($from_date, $to_date, $employee_id);

        $dataFormat = [];
        $tempArray = [];
        $present = null;


        if ($workingDates && $monthlyAttendanceData) {

            foreach ($workingDates as $data) {

                $flag = 0;

                foreach ($monthlyAttendanceData as $value) {

                    if ($data == $value->date && $value->m_in_time !== '') {
                        $flag = 1;
                        break;
                    } elseif ($data == $value->date && $value->af_in_time !== '') {
                        $flag = 1;
                        break;
                    } elseif ($data == $value->date && $value->e1_in_time != '') {
                        $flag = 1;
                        break;
                    } elseif ($data == $value->date && $value->e2_in_time !== '') {
                        $flag = 1;
                        break;
                    } elseif ($data == $value->date && $value->n_in_time !== '') {
                        $flag = 1;
                        break;
                    }
                }

                $tempArray['total_present'] = null;



                if ($flag == 0) {
                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date'] = $data;
                    $tempArray['m_in_time'] = '';
                    $tempArray['af_in_time'] = '';
                    $tempArray['e1_in_time'] = '';
                    $tempArray['e2_in_time'] = '';
                    $tempArray['n_in_time'] = '';
                    $tempArray['m_status'] = '';
                    $tempArray['af_status'] = '';
                    $tempArray['e1_status'] = '';
                    $tempArray['e2_status'] = '';
                    $tempArray['n_status'] = '';
                    $dataFormat[] = $tempArray;
                } else {

                    $tempArray['total_present'] = $present += 1;
                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date'] = $value->date;
                    $tempArray['m_in_time'] = $value->m_in_time;
                    $tempArray['af_in_time'] = $value->af_in_time;
                    $tempArray['e1_in_time'] = $value->e1_in_time;
                    $tempArray['e2_in_time'] = $value->e2_in_time;
                    $tempArray['n_in_time'] = $value->n_in_time;
                    $tempArray['m_status'] = $value->m_status;
                    $tempArray['af_status'] = $value->af_status;
                    $tempArray['e1_status'] = $value->e1_status;
                    $tempArray['e2_status'] = $value->e2_status;
                    $tempArray['n_status'] = $value->n_status;
                    $dataFormat[] = $tempArray;
                }
            }
        }
        //  dd($dataFormat);
        return $dataFormat;
    }

    public function number_of_working_days_date($from_date, $to_date, $employee_id)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $from_date . '","' . $to_date . '")'));
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }


        $target = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {

            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName = date("l", $timestamp);


            \array_push($workingDate, date('Y-m-d', $target));

            if (date('Y-m-d') <= date('Y-m-d', $target)) {
                break;
            }
            $target += (60 * 60 * 24);
        }
        return $workingDate;
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

    public function findAttendanceSummaryReport($month)
    {
        $data = findMonthToAllDate($month);

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'department_name', 'branch_name', 'designation_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')
            ->orderBy('employee.finger_id', 'ASC')->groupBy('employee.employee_id')
            ->where('status', UserStatus::$ACTIVE)->get();

        $start_date = $month . '-01';
        $end_date = date("Y-m-t", strtotime($start_date));

        $attendance = DB::table('view_employee_in_out_data')->select('finger_print_id', 'date', 'm_in_time', 'm_out_time', 'af_in_time', 'af_out_time', 'e1_in_time', 'e2_out_time', 'n_in_time', 'n_out_time')->whereBetween('date', [$start_date, $end_date])->get();

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {
            foreach ($data as $key => $value) {
                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;

                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeAttendance($attendance, $employee->finger_id, $value['date']);

                if ($hasAttendance) {

                    $tempArray['attendance_status'] = 'present';
                    $tempArray['leave_type'] = '';
                    $tempArray['gov_day_worked'] = 'no';
                } else {

                    $tempArray['attendance_status'] = 'absence';
                    $tempArray['gov_day_worked'] = 'no';
                    $tempArray['leave_type'] = '';
                }


                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }
        return $dataFormat;
    }

    public function hasEmployeeAttendance($attendance, $finger_print_id, $date)
    {
        foreach ($attendance as $key => $val) {
            if (($val->finger_print_id == $finger_print_id && $val->date == $date && $val->m_in_time != null
                // && $val->m_out_time != null
                && $val->af_in_time != null
                // && $val->af_out_time != null
                && $val->e1_in_time != null
                // && $val->e1_out_time != null
                && $val->e2_in_time != null
                // && $val->e2_out_time != null
                && $val->n_in_time != null
                // && $val->n_out_time != null
            )) {
                return true;
            }
        }
        return false;
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
            // dump('Holiday -'.$val);
            // dump('date -'.$date);
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
                // return $result;
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidaysDates = WeeklyHoliday::where('employee_id', $employee_id)->where('month', date('Y-m', strtotime($date)))->first();
        // dd($weeklyHolidaysDates, $employee_id, date('Y-m', strtotime($date)));

        $timestamp = strtotime($date);
        $dayName = date("l", $timestamp);
        foreach ($weeklyHolidays as $v) {
            if ($v->day_name == $dayName && $v->employee_id == $employee_id && isset($weeklyHolidaysDates) && $dayName == $weeklyHolidaysDates['day_name']) {
                $result['weekly_holiday'] = true;
                return $result;
            }
        }
        return $result;
    }
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
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('employee.finger_id', 'ASC')->whereRaw($qry)
            ->where('status', UserStatus::$ACTIVE)->where('finger_id', '!=', 1)->groupBy('employee.finger_id')->get();

        $attendance = DB::table('view_employee_in_out_data')->groupBy('date', 'finger_print_id')->orderBy('created_at', 'ASC')->whereBetween('date', [$start_date, $end_date])->get();
        // dd($attendance);
        // $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            foreach ($data as $key => $value) {


                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;

                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);

                if ($hasAttendance) {

                    $tempArray['m_in_time'] = $hasAttendance['m_in_time'];
                    $tempArray['af_in_time'] = $hasAttendance['af_in_time'];
                    $tempArray['e1_in_time'] = $hasAttendance['e1_in_time'];
                    $tempArray['e2_in_time'] = $hasAttendance['e2_in_time'];
                    $tempArray['n_in_time'] = $hasAttendance['n_in_time'];

                    // $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                } else {

                    $tempArray['m_in_time'] = '';
                    $tempArray['af_in_time'] = '';
                    $tempArray['e1_in_time'] = '';
                    $tempArray['e2_in_time'] = '';
                    $tempArray['n_in_time'] = '';
                    // $tempArray['employee_attendance_id'] = '';
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
                // $tempArray['designation_name'] = $employee->designation_name;
                // $tempArray['department_name'] = $employee->department_name;
                // $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);

                if ($hasAttendance) {

                    $tempArray['m_in_time'] = $hasAttendance['m_in_time'];
                    $tempArray['af_in_time'] = $hasAttendance['af_in_time'];
                    $tempArray['e1_in_time'] = $hasAttendance['e1_in_time'];
                    $tempArray['e2_in_time'] = $hasAttendance['e2_in_time'];
                    $tempArray['n_in_time'] = $hasAttendance['n_in_time'];
                } else {

                    $tempArray['m_in_time'] = '';
                    $tempArray['af_in_time'] = '';
                    $tempArray['e1_in_time'] = '';
                    $tempArray['e2_in_time'] = '';
                    $tempArray['n_in_time'] = '';
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        $excelFormat = [];
        $days = [];
        $sl = 1;
        $dataset = [];

        $sl = 0;
        $emptyArr = ['', '', '', '',  ''];

        foreach ($dataFormat as $key => $data) {
            // dump($data);
            // dd($data);

            $sl++;
            $firstBranch = ['MS-InTime'];
            $fsinTimeInfo = ['AF-InTime'];
            $fsoutTimeInfo = ['E1-InTime'];
            $secondBranch = ['E2-InTime'];
            $ssinTimeInfo = ['NS-InTime'];

            for ($i = 0; $i < count($data); $i++) {
                $employeeData = [$sl,  $data[0]['finger_id'], $data[0]['fullName']];


                $fsinTimeInfo[] = $data[$i]['m_in_time'] != null ? date('H:i', strtotime($data[$i]['m_in_time'])) : '00:00';
                $fsoutTimeInfo[] = $data[$i]['af_in_time'] != null ? date('H:i', strtotime($data[$i]['af_in_time'])) : '00:00';
                $ssinTimeInfo[] = $data[$i]['e1_in_time'] != null ? date('H:i', strtotime($data[$i]['e1_in_time'])) : '00:00';
                $ssoutTimeInfo[] = $data[$i]['e2_in_time'] != null ? date('H:i', strtotime($data[$i]['e2_in_time'])) : '00:00';
                $workingTimeInfo[] = $data[$i]['n_in_time'] != null ? date('H:i', strtotime($data[$i]['n_in_time'])) : '00:00';
                // $permissionInfo[] = $permission_exist == 1 ? date('H:i', strtotime($permission->permission_duration)) : '00:00';
                // }
            }


            $excelFormat[] = array_merge($employeeData, $firstBranch);
            $excelFormat[] = array_merge($emptyArr, $fsinTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $fsoutTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $secondBranch);
            $excelFormat[] = array_merge($emptyArr, $ssinTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $ssoutTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $workingTimeInfo);
        }
        return $excelFormat;
    }

    public function hasEmployeeMusterAttendance($attendance, $finger_print_id, $date)
    {
        $dataFormat = [];
        $dataFormat['first_device'] = '';
        $dataFormat['m_in_time'] = '';
        $dataFormat['af_in_time'] = '';
        $dataFormat['e1_in_time'] = '';
        $dataFormat['e2_in_time'] = '';
        $dataFormat['n_in_time'] = '';
        $dataFormat['m_out_time'] = '';
        $dataFormat['af_out_time'] = '';
        $dataFormat['e1_out_time'] = '';
        $dataFormat['e2_out_time'] = '';
        $dataFormat['n_out_time'] = '';

        if (count($attendance) != 0) {
            foreach ($attendance as $key => $val) {
                if (($val->finger_print_id == $finger_print_id && $val->date == $date)) {

                    $dataFormat['m_in_time'] = $val->m_in_time;
                    $dataFormat['af_in_time'] = $val->af_in_time;
                    $dataFormat['e1_in_time'] = $val->e1_in_time;
                    $dataFormat['e2_in_time'] = $val->e2_in_time;
                    $dataFormat['n_in_time'] = $val->n_in_time;
                    $dataFormat['m_out_time'] = $val->m_out_time;
                    $dataFormat['af_out_time'] = $val->af_out_time;
                    $dataFormat['e1_out_time'] = $val->e1_out_time;
                    $dataFormat['e2_out_time'] = $val->e2_out_time;
                    $dataFormat['n_out_time'] = $val->n_out_time;
                    $dataFormat['m_status'] = $val->m_status;
                    $dataFormat['af_status'] = $val->af_status;
                    $dataFormat['e1_status'] = $val->e1_status;
                    $dataFormat['e2_status'] = $val->e2_status;
                    $dataFormat['n_status'] = $val->n_status;

                    $dataFormat['employee_attendance_id'] = $val->employee_attendance_id;
                }
            }
        }
        return $dataFormat;
    }
}
