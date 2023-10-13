<?php

namespace App\Http\Controllers\View;

use DateTime;
use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Employee;
use App\Model\WorkShift;
use Carbon\CarbonPeriod;
use App\Model\Department;
use Illuminate\Http\Request;
use App\Model\LeaveApplication;
use App\Model\EmployeeInOutData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Model\ViewEmployeeInOutData;
use App\Lib\Enumerations\LeaveStatus;
use App\Repositories\LeaveRepository;
use App\Lib\Enumerations\AttendanceStatus;
use App\Repositories\AttendanceRepository;

class ManualAttendanceReportController extends Controller
{
    protected $leaveRepository;
    protected $attendanceRepository;

    public function __construct(LeaveRepository $leaveRepository, AttendanceRepository $attendanceRepository)
    {
        $this->leaveRepository = $leaveRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function attendance(Request $request)
    {

        set_time_limit(0);

        $bug             = null;
        $data_format     = [];
        $count_total_log = 0;

        $date  = $request->from_date;
        $date1 = $request->to_date;

        $yesterday = Carbon::today()->subDays(1);

        $from = date('Y-m-d', strtotime($date));
        $to   = date('Y-m-d', strtotime($date1));

        $totalDates = CarbonPeriod::create($from, $to);

        $fromDate = Carbon::CreateFromFormat('Y-m-d', $from);
        $ToDate   = Carbon::CreateFromFormat('Y-m-d', $to);

        $fromMonth = date('m', strtotime($date));
        $ToMonth   = date('m', strtotime($date1));

        $check_from = ($fromDate <= $yesterday);
        $check_to   = ($ToDate <= $yesterday);

        $totalDates = $totalDates->toArray();

        // foreach ($totalDates as $value) {
        //     echo ($value->format('Y-m-d'));
        //     $data = DB::table('ms_sql')->whereRaw("datetime >= '" . $value . "'")->where('status', 0)->count();
        //     $count_total_log += $data;

        //     // dump($value, $check_from . ',' . $check_to);
        //     // dd();
        //     if ($data == 0) {
        //         return \redirect()->back()->with('error', 'Attendance Log not found on : ' . date('Y-m-d', \strtotime($value)));
        //     }
        // }
        // dd('asdasd');

        if ($fromMonth != $ToMonth) {

            return \redirect()->back()->with('error', 'Kindly select dates from same month...');

        } elseif (count($totalDates) > 5) {

            return \redirect()->back()->with('error', 'Selected date ranges exceeds 5 days...');

        }

        // elseif ($check_from == false || $check_to == false) {

        //     return \redirect()->back()->with('error', 'Report cannot br generated for recent dates...');

        // }

        elseif (!$date && !$date1) {

            return \redirect()->back()->with('error', 'Kindly Select From and To Dates...');

        } else {

            $employees = Employee::select('finger_id')->groupby('finger_id')->get();

            foreach ($totalDates as $key => $value) {
                $date = $value->format('Y-m-d');

                foreach ($employees as $key1 => $finger_id) {

                    // $rework = EmployeeInOutData::whereRaw("date= '" . $date . "' and finger_print_id= '" . $finger_id->finger_id . "'")->first();
                    // $secondRun = false;
                    // if ($rework) {
                    //     $secondRun = true;
                    // }
    
                    $start_date = DATE('Y-m-d', strtotime($date)) . " 05:30:00";
                    $end_date = DATE('Y-m-d', strtotime($date . " +1 day")) . " 06:30:00";
    
                    $data_format = $this->calculate_attendance($start_date, $end_date, $finger_id);
                    $shift_list = WorkShift::orderBy('start_time', 'ASC')->get();
    
                    //find employee over time
                    if ($data_format != [] && isset($data_format['working_hour'])) { //
     
                        //$workingTime = new DateTime($data_format['working_time']);
                        $workingTime = new DateTime($data_format['working_hour']);
                        $actualTime = new DateTime('08:00:00');
    
                        //\Log::info(print_r($workingTime)." ".print_r($actualTime));
    
                        if ($workingTime > $actualTime) {
    
                            $over_time = $actualTime->diff($workingTime);
    
                            $roundMinutes = (int) $over_time->i >= 30 ? '30' : '00';
                            $roundHours = (int) $over_time->h >= 1 ? sprintf("%02d", ($over_time->h)) : '00';
    
                            if ($over_time->i >= 30 || $over_time->h >= 1) {
                                $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                                $data_format['over_time'] = $roundHours . ':' . $roundMinutes;
                            } else {
                                $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                                $data_format['over_time'] = null;
                            }
                        } else {
                            $data_format['attendance_status'] = AttendanceStatus::$LESSHOURS;
                            $data_format['over_time'] = null;
                        }
    
                        // find employee early or late time and shift name
                        if ($data_format != [] && isset($data_format['in_time']) && isset($data_format['out_time'])) {
    
                            foreach ($shift_list as $key => $value) {
    
                                $in_time = new DateTime($data_format['in_time']);
                                $login_time = date('H:i:s', \strtotime($data_format['in_time']));
                                $start_time = new DateTime($data_format['date'] . ' ' . $value->start_time);
    
                                $buffer_start_time = Carbon::createFromFormat('H:i:s', $value->start_time)->subMinutes(29)->format('H:i:s');
                                $buffer_end_time = Carbon::createFromFormat('H:i:s', $value->start_time)->addMinutes(120)->format('H:i:s');
    
                                $emp_shift = $this->shift_timing_array($login_time, $buffer_start_time, $buffer_end_time);
    
                                info('---------------------------------------------------------------');
                                info($finger_id->finger_id);
                                info($date);
                                // info($buffer_start_time);
                                // info($login_time);
                                // info($buffer_end_time);
                                // info($emp_shift ? 1 : 0);
                                // info('---------------------------------------------------------------');
    
                                if ($emp_shift == \true) {
    
                                    if ($in_time >= $start_time) {
    
                                        info($value->shift_name);
                                        $interval = $in_time->diff($start_time);
                                        $data_format['shift_name'] = $value->shift_name;
                                        $data_format['late_by'] = $interval->format('%H') . ":" . $interval->format('%I');
    
                                    } elseif ($in_time <= $start_time) {
    
                                        info($value->shift_name);
                                        $interval = $start_time->diff($in_time);
                                        $data_format['shift_name'] = $value->shift_name;
                                        $data_format['early_by'] = $interval->format('%H') . ":" . $interval->format('%I');
    
                                    }
                                }
                            }
                        } else {
    
                            $data_format['early_by'] = null;
                            $data_format['late_by'] = null;
    
                        }
                    }
    
                   // \Log::info(print_r($data_format));
                    //insert employee attendacne data to report table
                    if ($data_format != [] && (isset($data_format['working_hour']) || isset($data_format['in_time']) || isset($data_format['out_time']))) {
                        $workingTime = explode(':', $data_format['working_hour']);
    
                        if ($workingTime[0] >= 0) {
                            $if_exists = EmployeeInOutData::where('finger_print_id', $data_format['finger_print_id'])->where('date', $data_format['date'])->first();
    
                            if (!$if_exists) {
    
                                //  $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                                EmployeeInOutData::insert($data_format);
                            } else {
                                //  $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                                EmployeeInOutData::where('date', $data_format['date'])->where('finger_print_id', $data_format['finger_print_id'])->update($data_format);
                            }
                        }
                    } else {
    
                        $if_exists = EmployeeInOutData::where('finger_print_id', $finger_id->finger_id)->where('date', date('Y-m-d', \strtotime($start_date)))->first();
    
                        $data_format = [
                            'date' => date('Y-m-d', \strtotime($start_date)),
                            'finger_print_id' => $finger_id->finger_id,
                            'in_time' => null,
                            'out_time' => null,
                            'working_time' => null,
                            'working_hour' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'created_by' => isset(auth()->user()->user_id) ? auth()->user()->user_id : null,
                            'updated_by' => isset(auth()->user()->user_id) ? auth()->user()->user_id : null,
                            'status' => 1,
                        ];
    
                        $tempArray = [];
    
                        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $date . '","' . $date . '")'));
    
                        $leave = LeaveApplication::select('application_from_date', 'application_to_date', 'employee_id', 'leave_type_name')
                            ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
                            ->where('status', LeaveStatus::$APPROVE)
                            ->where('application_from_date', '>=', $date)
                            ->where('application_to_date', '<=', $date)
                            ->get();
    
                        $hasLeave = $this->attendanceRepository->ifEmployeeWasLeave($leave, $finger_id->employee_id, $date);
                        if ($hasLeave) {
                            $tempArray['attendance_status'] = AttendanceStatus::$LEAVE;
                        } else {
                            if ($date > date("Y-m-d")) {
                                $tempArray['attendance_status'] = AttendanceStatus::$FUTURE;
                            } else {
                                $ifHoliday = $this->attendanceRepository->ifHoliday($govtHolidays, $date, $finger_id->employee_id);
                                if ($ifHoliday['weekly_holiday'] == true) {
                                    $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                                } elseif ($ifHoliday['govt_holiday'] == true) {
                                    $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                                } else {
                                    $tempArray['attendance_status'] = AttendanceStatus::$ABSENT;
                                }
                            }
                        }
                        if (!$if_exists) {
                            $data_format['attendance_status'] = $tempArray['attendance_status'];
                            // echo "<br> created <pre>" . print_r($data_format) . "</pre>";
                            EmployeeInOutData::insert($data_format);
                        } else {
                            $data_format['attendance_status'] = $tempArray['attendance_status'];
                            // echo "<br> updated <pre>" . print_r($data_format) . "</pre>";
                            $if_exists->update($data_format);
                            $if_exists->save();
                        }
                    }
    
            }  

            }

        }

        if ($bug != 0) {

            return \redirect('dashboard')->with('error', 'Something Went Wrong! Please Try Again...');

        } else {

            return \redirect('dashboard')->with('success', 'Attendance Report Generated Successfully...');

        }
    }

  
    public function calculate_attendance($date_from, $date_to, $finger_id, $reRun = true)
    {
        $k = 0;
        $a = 0;
        $first_row = 0;
        $at = [];
        $bt = [];
        $first_row_2 = 0;
        $at_id = [];
        $bt_id = [];
        $in_out_time_at = [];
        $in_out_time_bt = [];
        $attendance_data = [];
        $device_name_at = [];
        $device_name_bt = [];
        \set_time_limit(0);

        // $results = DB::table('ms_sql')
        //     ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
        //     ->where('ID', $finger_id->finger_id)
        //     ->where('status', 0)
        //     ->orderby('datetime', 'ASC')
        //     ->get();

        if ($reRun) {
            $results = DB::table('ms_sql')
                ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
                ->where('ID', $finger_id->finger_id)
                ->orderby('datetime', 'ASC')
                ->get();
        }

        if (count($results) == 1 && $results[0]->type == 'IN') {

            Log::info("Only one results for : " . $finger_id->finger_id . ' Date : ' . $date_from);
            $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
            $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['out_time'] = \null;
            $attendance_data['working_time'] = \null;
            $attendance_data['working_hour'] = \null;
            $attendance_data['status'] = 2;
            $attendance_data['attendance_status'] = AttendanceStatus::$ONETIMEINPUNCH;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['in_out_time'] = date('H:i', strtotime($results[0]->datetime)) . ":" . $results[0]->type;

            return $attendance_data;
        } elseif (count($results) == 1 && $results[0]->type == 'OUT') {

            Log::info("Only one results for : " . $finger_id->finger_id . ' Date : ' . $date_from);
            $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
            $attendance_data['out_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['in_time'] = \null;
            $attendance_data['working_time'] = \null;
            $attendance_data['working_hour'] = \null;
            $attendance_data['status'] = 2;
            $attendance_data['attendance_status'] = AttendanceStatus::$ONETIMEOUTPUNCH;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['in_out_time'] = date('H:i', strtotime($results[0]->datetime)) . ":" . $results[0]->type;

            return $attendance_data;
        } elseif (count($results) > 1) {

            $count_check = 0;
            $count_check_2 = 0;
            $count_check_3 = 0;
            $primary_id=$primary_id_2=$primary_id_3=[];
            $in_out_time_record=$in_out_time_record_2=$in_out_time_record_3=[];

            for ($i = 0; $i < count($results); $i++) {
                if ($results[$i]->type == 'IN') {
                    $count_check++;
                    array_push($primary_id, $results[$i]->primary_id);
                    array_push($in_out_time_record, (date('H:i', strtotime($results[$i]->datetime)) . ':' . $results[$i]->type));
                }
            }

            for ($i = 0; $i < count($results); $i++) {
                if ($results[$i]->type == 'OUT') {
                    $count_check_2++;
                    array_push($primary_id_2, $results[$i]->primary_id);
                    array_push($in_out_time_record_2, (date('H:i', strtotime($results[$i]->datetime)) . ':' . $results[$i]->type));
                }
            }

            for ($i = 0; $i < count($results); $i++) {
                if ($results[0]->type == 'OUT') {
                    $count_check_3++;
                    array_push($primary_id_3, $results[0]->primary_id);
                    array_push($in_out_time_record_3, (date('H:i', strtotime($results[$i]->datetime)) . ':' . $results[$i]->type));
                } elseif ($i != 0 && $results[$i]->type == 'IN') {
                    $count_check_3++;
                    array_push($primary_id_3, $results[$i]->primary_id);
                    array_push($in_out_time_record_3, (date('H:i', strtotime($results[$i]->datetime)) . ':' . $results[$i]->type));
                }
            }

            if ($count_check == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['out_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['working_hour'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id)->update(['status' => 1]);

                return $attendance_data;

            } elseif ($count_check_2 == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['out_time'] = date('Y-m-d H:i:s', strtotime($results[count($results) - 1]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['in_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['working_hour'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record_2);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id_2)->update(['status' => 1]);

                return $attendance_data;
            } elseif ($count_check_3 == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[1]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['out_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['working_hour'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record_3);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id_3)->update(['status' => 1]);

                return $attendance_data;
            }
        }

        foreach ($results as $key => $row) {

            $init = MsSql::where('primary_id', $row->primary_id)->update(['status' => 2]);

            $attendance_data['device_name'] = $row->device_name;

            if ($first_row == 0 && $row->type == "OUT") {
                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);
                // echo 'at first row OUT <br>';
                continue;
            } elseif (!isset($at[$k]['fromdate']) && $row->type == "OUT" && $first_row_2 == 0) {
                $j = $k;
                $j--;
                if (!isset($at[$j]['fromdate'])) {
                    array_push($at_id, $row->primary_id);
                    array_push($in_out_time_at, $row->type);
                    array_push($device_name_at, $row->device_name);
                    // echo 'at first row 2 OUT <br>';

                    continue;
                }
            } elseif (isset($at[$k]['fromdate']) && $row->type == "IN" && $first_row_2 == 0) {

                $datetime1 = new DateTime($at[$k]['fromdate']);
                $datetime2 = new DateTime($row->datetime);
                $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                $pieces = explode(":", $subtotal);
                if ($pieces[0] > 13) {

                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);
                    array_push($in_out_time_bt, $row->type);
                    array_push($device_name_bt, $row->device_name);

                    $first_row_2 = 1;
                    // echo ' bt first_row_2 = 0  - >9 <br>';

                    continue;
                }

                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);
                // echo ' at first_row_2 = 0 - <9 <br>';

                continue;
            }

            if ($row->type == "IN") {
                $j = $k;
                $j--;
                if ($first_row_2 == 1) {
                    if (isset($bt[$a]['fromdate'])) {
                        array_push($at_id, $row->primary_id);
                        array_push($in_out_time_at, $row->type);
                        array_push($device_name_at, $row->device_name);
                        // echo 'IN at first_row_2 = 1  <br>';

                        continue;
                    }
                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);
                    array_push($in_out_time_bt, $row->type);
                    array_push($device_name_bt, $row->device_name);

                    $first_row_2 = 1;
                    // echo 'IN at first_row_2 = 1  <br>';
                    continue;
                }
                if ($k > 0) {
                    $j = $k;
                    $j--;

                    $datetime1 = new DateTime($at[$j]['todate']);
                    $datetime2 = new DateTime($row->datetime);
                    $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                    $pieces = explode(":", $subtotal);
                    if ($pieces[0] > 13) {
                        if (isset($bt[$a]['fromdate'])) {
                            array_push($bt_id, $row->primary_id);
                            array_push($in_out_time_bt, $row->type);
                            array_push($device_name_bt, $row->device_name);
                            // echo 'IN bt first_row_2 = 1 - > 9 <br>';

                            continue;
                        }
                        $bt[$a]['fromdate'] = $row->datetime;
                        $bt[$a]['statusin'] = $row->type;
                        array_push($bt_id, $row->primary_id);
                        array_push($in_out_time_bt, $row->type);
                        array_push($device_name_bt, $row->device_name);
                        // echo 'IN bt first_row_2 = 1 - < 9 <br>';

                        $first_row_2 = 1;
                        continue;
                    }
                }
                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);

                $at[$k]['fromdate'] = $row->datetime;
                $at[$k]['statusin'] = $row->type;
                $first_row = 1;
                continue;
            }

            if ($row->type == "OUT") {
                if ($first_row_2 == 0) {
                    if (isset($at[$k]['fromdate']) && $at[$k]['fromdate'] != "") {
                        $datetime1 = new DateTime($at[$k]['fromdate']);
                        $datetime2 = new DateTime($row->datetime);
                        $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                        $pieces = explode(":", $subtotal);
                        if ($pieces[0] > 13) {
                            array_push($at_id, $row->primary_id);
                            array_push($in_out_time_at, $row->type);
                            array_push($device_name_at, $row->device_name);
                            // echo 'OUT at first_row_2 = 0  <br>';

                            continue;
                        }
                        $at[$k]['statusout'] = $row->type;
                        $at[$k]['todate'] = $row->datetime;
                        $at[$k]['subtotalhours'] = $subtotal;
                        array_push($at_id, $row->primary_id);
                        array_push($in_out_time_at, $row->type);
                        array_push($device_name_at, $row->device_name);
                        // echo 'OUT at first_row_2 = 0  <br>';

                        $k++;
                        continue;
                    } elseif (!isset($at[$k]['todate'])) {
                        if (isset($at[$j]['fromdate'])) {
                            $j = $k;
                            $j--;
                            $datetime1 = new DateTime($at[$j]['fromdate']);
                            $datetime2 = new DateTime($row->datetime);
                            $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                            $at[$j]['todate'] = $row->datetime;
                            $at[$j]['statusout'] = $row->type;
                            $at[$j]['subtotalhours'] = $subtotal;
                            array_push($at_id, $row->primary_id);
                            array_push($in_out_time_at, $row->type);
                            array_push($device_name_at, $row->device_name);
                            //  echo 'OUT at first_row_2 != 0 <br>';

                            continue;
                        }
                    }
                } else {
                    if (isset($bt[$a]['fromdate']) && $bt[$a]['fromdate'] != "") {
                        $bt[$a]['statusout'] = $row->type;
                        $bt[$a]['todate'] = $row->datetime;
                        $datetime1 = new DateTime($bt[$a]['fromdate']);
                        $datetime2 = new DateTime($row->datetime);
                        $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                        $bt[$a]['subtotalhours'] = $subtotal;
                        array_push($bt_id, $row->primary_id);
                        array_push($in_out_time_bt, $row->type);
                        array_push($device_name_bt, $row->device_name);
                        // echo 'isset bt fromdate  <br>';

                        $a++;
                        continue;
                    } elseif (!isset($bt[$a]['todate'])) {
                        $j = $a;
                        $j--;
                        if (isset($bt[$j]['fromdate'])) {
                            $datetime1 = new DateTime($bt[$j]['fromdate']);
                            $datetime2 = new DateTime($row->datetime);
                            $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                            $bt[$j]['todate'] = $row->datetime;
                            $bt[$j]['statusout'] = $row->type;
                            $bt[$j]['subtotalhours'] = $subtotal;
                            array_push($bt_id, $row->primary_id);
                            array_push($in_out_time_bt, $row->type);
                            array_push($device_name_bt, $row->device_name);
                            // echo 'isset bt fromdate !todate  <br>';

                            continue;
                        }
                    }
                }
            }
        }

        if (count($at) > 0) {
            if (!isset($at[count($at) - 1]['todate'])) {
                unset($at[count($at) - 1]);
            }
        }

        if (count($bt) > 0) {
            if (!isset($bt[count($bt) - 1]['todate'])) {
                unset($bt[count($bt) - 1]);
            }
        }

        for ($i = 0; $i < count($at); $i++) {
            // echo $at[$i]['fromdate'] . "  -  " . $at[$i]['todate'] . "  ---  " . $at[$i]['subtotalhours'];
            //  echo "<br>";
        }
        $work1 = $this->calculate_total_working_hours($at);

        "<br>-------------------------<br>";
        for ($i = 0; $i < count($bt); $i++) {
            $bt[$i]['fromdate'] . "  -  " . $bt[$i]['todate'] . "  ---  " . $bt[$i]['subtotalhours'];
            "<br>";
        }
        $work2 = $this->calculate_total_working_hours($bt);
        $work2;

        if (count($bt) > 0) {
            $work1_hours = explode(":", $work1);
            $work2_hours = explode(":", $work2);
            if ($work2_hours > $work1_hours) {
                $at = $bt;
                $at_id = $bt_id;
                $in_out_time_at = $in_out_time_bt;
                $device_name_at = $device_name_bt;
            }
        }

        for ($i = 0; $i <= count($at_id) - 1; $i++) {
            $sql = "update ms_sql3 set status=1 where primary_id=" . $at_id[$i];
            //echo "<br>".$sql."</br>";
            //$mysqli->query($sql);
        }
        // echo "<br><pre>";
        // print_r($at_id);
        // print_r($in_out_time_at);
        // echo "</br>";

        if (isset($at[0]['fromdate'])) {
            $currnet_date = Carbon::createFromFormat('Y-m-d H:i:s', $at[0]['fromdate'])->format('Y-m-d');
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_from)->format('Y-m-d');
            if ($currnet_date != $from_date) {
                $k = 0;
                $a = 0;
                $first_row = 0;
                $at = [];
                $bt = [];
                $first_row_2 = 0;
                $at_id = [];
                $bt_id = [];
                $in_out_time_at = [];
                $in_out_time_bt = [];
                $device_name_at = [];
                $device_name_bt = [];
                $attendance_data = [];
            }
        }

        $update_status = true;

        if (count($at) > 0) {
            foreach ($at_id as $primary_id) {

                // echo 'Primary ID ' . $primary_id;
                $upd_to_date = $at[count($at) - 1]['todate'];
                $check_by_primary = MsSql::where('primary_id', $primary_id)->first();

                if ($update_status == true) {

                    $update = DB::table('ms_sql')->where('primary_id', $primary_id)->update(['status' => 1]);
                    // echo "<br>";
                    // echo "Update status = " . $update_status;
                    // echo "<br>";
                }
                if ($upd_to_date == $check_by_primary->datetime) {
                    $update_status = false;
                }
            }
        }

        // Attendance data set return values...................................
        for ($i = 0; $i < count($at); $i++) {

            if ($i == 0) {
                $attendance_data['date'] = date('Y-m-d', strtotime($at[$i]['fromdate']));
                $attendance_data['in_time'] = $at[$i]['fromdate'];
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                // $attendance_data['finger_print_id'] = $finger_id['ID'];
            }
            $attendance_data['out_time'] = $at[count($at) - 1]['todate'];
            // $attendance_data['working_time'] = $this->calculate_total_working_hours($at);
            $attendance_data['working_time'] = $this->workingtime($at[0]['fromdate'], $at[count($at) - 1]['todate']);
            $attendance_data['working_hour'] = $this->calculate_total_working_hours($at);
            // $attendance_data['working_hour'] = $this->workingtime($at[0]['fromdate'], $at[count($at) - 1]['todate']);
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
        }

        //dd($attendance_data);

        if ($attendance_data != []) {
            if (count($at) > 0 && count($at_id) > 0) {

                $attendance_data['in_out_time'] = $this->in_out_time($at_id, $in_out_time_at, $device_name_at);
            }
        }

        // echo "<pre>";
        // print_r($at);
        //  print_r($attendance_data);
        //  echo "</pre>";

        return $attendance_data;
    }

    public function in_out_time($at_id, $in_out_time_at, $device_name_at)
    {
        $result = [];
        $array_values = array_values($at_id);
        $array_values = json_encode($at_id);

        foreach ($at_id as $key => $primary_id) {
            $in_out_time = DB::table('ms_sql')->where('primary_id', $primary_id)->select('datetime')->first();
            if($device_name_at[$key])
                $result[] = date('H:i', strtotime($in_out_time->datetime)) . ':' . $in_out_time_at[$key] . ' ' . '(' . $device_name_at[$key] . ')';
            else
                $result[] = date('H:i', strtotime($in_out_time->datetime)) . ':' . $in_out_time_at[$key];
        }
        // dd($result);
        $str = json_encode($result);
        $str = str_replace('[', ' ', $str);
        $str = str_replace(']', ' ', $str);
        $str = str_replace('"', ' ', $str);
        // dd($str);
        return $str;
    }

    public function calculate_hours_mins($datetime1, $datetime2)
    {
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%h') . ":" . $interval->format('%i') . ":" . $interval->format('%s');
    }

    public function calculate_total_working_hours($at)
    {
        $total_seconds = 0;
        for ($i = 0; $i < count($at); $i++) {
            $seconds = 0;
            $timestr = $at[$i]['subtotalhours'];

            $parts = explode(':', $timestr);

            $seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
            $total_seconds += $seconds;
        }
        return gmdate("H:i:s", $total_seconds);
    }


    public function find_closest_time($dates, $first_in)
    {

        function closest($dates, $findate)
        {
            $newDates = array();

            foreach ($dates as $date) {
                $newDates[] = strtotime($date);
            }

            // echo "<pre>";
            // print_r($newDates);
            // echo "</pre>";

            sort($newDates);
            foreach ($newDates as $a) {
                if ($a >= strtotime($findate)) {
                    return $a;
                }
            }
            return end($newDates);
        }

        $values = closest($dates, date('Y-m-d H:i:s', \strtotime($first_in)));
        // echo date('Y-m-d H:i:s', $values);
    }

    public function shift_timing_array($in_time, $start_shift, $end_shift)
    {
        $shift_status = $in_time <= $end_shift && $in_time >= $start_shift;
        return $shift_status;
    }


    public function workingtime($from, $to)
    {
        $date1 = new DateTime($to);
        $date2 = $date1->diff(new DateTime($from));
        $hours = ($date2->days * 24);
        $hours = $hours + $date2->h;

        return $hours . ":" . $date2->i . ":" . $date2->s;
    }

    public function in_out_time_list($in_out_time_list)
    {
        $result = [];

        foreach ($in_out_time_list as $key => $in_out_time) {
            $result[] = $in_out_time;
        }

        $str = json_encode($result);
        $str = str_replace('[', ' ', $str);
        $str = str_replace(']', ' ', $str);
        $str = str_replace('"', ' ', $str);
        return $str;
    }
}
