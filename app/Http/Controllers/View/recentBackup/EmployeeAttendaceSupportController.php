<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Model\EmployeeAttendance;
use App\Model\MsSql;
use App\Model\ViewEmployeeInOutData;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAttendaceSupportController extends Controller
{

    public function insertStatus()
    {
       

    //    foreach ($ids as $primary_id) {
    //         echo  "<br>";
    //         echo  $primary_id;
    //         echo  "</br>";
    //         DB::table('ms_sql')->where('primary_id', $primary_id)->update(['status' => 1]);
    //     }
    }

    public function workingtime($from, $to)
    {
        $date1 = new DateTime($to);
        $date2 = $date1->diff(new DateTime($from));
        $hours = ($date2->days * 24);
        $hours = $hours + $date2->h;

        return $hours . ":" . $date2->i . ":" . $date2->s;
    }



    public function addhours($arr)
    {
        $total = 0;
        foreach ($arr as $element) :
            $temp = explode(":", $element);
            $total += (int)$temp[0] * 3600;
            $total += (int)$temp[1] * 60;
            $total += (int)$temp[2];
        endforeach;

        $formatted = sprintf(
            '%02d:%02d:%02d',
            ($total / 3600),
            ($total / 60 % 60),
            $total % 60
        );
        return $formatted;
    }


    public function subhours($arr)
    {
        $total = 0;
        foreach ($arr as $element) :
            $temp = explode(":", $element);
            $total += (int)$temp[0] * 3600;
            $total += (int)$temp[1] * 60;
            $total += (int)$temp[2];
        endforeach;

        $formatted = sprintf(
            '%02d:%02d:%02d',
            ($total / 3600),
            ($total / 60 % 60),
            $total % 60
        );
        $compare  = new DateTime('23:59:59');
        $diff     = new DateTime($formatted);
        $interval = $compare->diff($diff);
        $range    = $diff > new DateTime('12:00:00');
        $time     = $interval->format('%H:%i:%s');
        return $range ? $time : $formatted;
    }



    public function attendance_old()
    {

        $inc         = 0;
        $in          = "";
        $first_in    = "";
        $tot         = [];
        $data_set    = [];
        $in_out_time = [];
        $employee_id = "";
        $in_pid      = "";
        $log_in_pid  = "";
        $day_log = 0;

        $results = DB::table("ms_sql")->where('status', 0)->orderBy('ID', 'ASC')->orderBy('datetime', 'ASC')->limit(2500)->get();

        foreach ($results as $key => $obj) {

            $raw_data = MsSql::where('datetime', $obj->datetime)->where('id', $obj->ID)->first();
            $ifExists = EmployeeAttendance::where('finger_print_id', $obj->ID)->where('in_out_time', $obj->datetime)->where('inout_status', 0)->first();

            if (!$ifExists) {
                $in_out_log_id = DB::table('employee_attendance')->insertGetID([
                    'in_out_time'     => $obj->datetime,
                    'finger_print_id' => $obj->ID,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]);
                $log_data = EmployeeAttendance::where('employee_attendance_id', $in_out_log_id)->first();
            } else {
                $log_data = $ifExists;
            }

            if ($inc % 2 == 0) {
                if (($employee_id && ($employee_id != $obj->ID))) {
                    $first_in = "";
                    $tot      = [];
                    echo "employee_id != obj->ID";
                    echo "<br>";
                }

                $in = $obj->datetime;

                if (!$first_in) {
                    $first_in = $obj->datetime;
                }

                $in_out_time[$obj->ID] = ["in_time" => $first_in];

                $employee_id = $obj->ID;
                $in_pid      = $raw_data->ID;
                $log_in_pid  = $log_data->employee_attendance_id;

                $log_data->inout_status = 0;
                $log_data->save();

                $raw_data->status = 0;
                $raw_data->save();
            } else {

                if ($employee_id == $obj->ID) {

                    $log_data->inout_status = 1;
                    $log_data->save();

                    $raw_data->status = 1;
                    $raw_data->save();

                    MsSql::where('ID', $in_pid)->update(['status' => 1]);

                    $out      = $this->workingtime($in, $obj->datetime);
                    $in_time  = DATE('Y-m-d', strtotime($first_in)) . " 19:55:00";
                    $out_time = DATE('Y-m-d', strtotime($first_in . " +1 day")) . " 10:00:00";

                    $log_time = $obj->datetime;


                    $in_date = DATE("Y-m-d", strtotime($first_in));
                    $log_date = DATE("Y-m-d", strtotime($log_time));

                    if (strtotime($in_date) < strtotime($log_date) && ($day_log == 1)) {
                        $first_in = $in;
                        $tot = [];
                    }


                    $dayin_time  = DATE('Y-m-d', strtotime($first_in)) . " 05:30:00";
                    $dayout_time = DATE('Y-m-d', strtotime($first_in)) . " 23:00:00";


                    echo "First In : " . $first_in;
                    echo "<br>";
                    echo $obj->ID . " == " . DATE('d-m-Y H:i:s', strtotime($in)) . " == " . DATE('d-m-Y H:i:s', strtotime($obj->datetime));
                    echo "<br>";
                    echo "Working Time : " . $out;
                    echo "<br>";
                    echo "in date =>" . $in_date . " || outdate => " . $log_date . "Day Log" . $day_log . "<br>";



                    $expl_out = explode(":", $out);
                    if ($expl_out[0] > "10") {
                        $inc = 0;
                        echo "***************<br>";
                        $first_in    = $obj->datetime;
                        $employee_id = $obj->ID;
                        $in          = $obj->datetime;
                        $tot         = [];

                        EmployeeAttendance::where('employee_attendance_id', $log_in_pid)->update(['inout_status' => 2]);

                        $log_data->inout_status = 0;
                        $log_data->save();

                        $raw_data->status = 0;
                        $raw_data->save();
                        $day_log = 0;
                    } elseif ((strtotime($log_time) > strtotime($dayin_time)) && (strtotime($log_time) < strtotime($dayout_time))) {

                        // $in_date=DATE("Y-m-d",strtotime($first_in));
                        // $log_date=DATE("Y-m-d",strtotime($log_time));
                        // echo "in date".$in_date." outdate".$log_date."<br>";
                        // if(strtotime($in_date) < strtotime($log_date)){
                        //     $first_in=$in;
                        //     $tot=[];
                        // }
                        $day_log = 1;
                        $tot[] = $out;
                        print_r($tot);
                        echo "<br>";

                        echo "Day Shift Total Hours : " . $this->addhours($tot);
                        echo "<br>";


                        $data_set[$obj->ID][$first_in] = ["hours" => $this->addhours($tot), "out_time" => $obj->datetime, 'in_time_from' => $dayin_time, 'out_time_upto' => $dayout_time];
                    } elseif ((strtotime($log_time) > strtotime($in_time)) && (strtotime($log_time) < strtotime($out_time))) {

                        $tot[] = $out;
                        print_r($tot);
                        echo "<br>";
                        echo "Night Shift Total Hours : " . $this->addhours($tot);
                        echo "<br>";

                        $data_set[$obj->ID][$first_in] = ["hours" => $this->addhours($tot), "out_time" => $obj->datetime, 'in_time_from' => $in_time, 'out_time_upto' => $out_time];
                        $day_log = 0;
                    } else {
                        $first_in                      = $in;
                        $in                            = "";
                        $tot                           = [];
                        $tot[]                         = $out;
                        $data_set[$obj->ID][$first_in] = ["hours" => $out, "out_time" => "", 'out_time_upto' => "", 'in_time_from' => ""];
                        $day_log = 0;
                        echo "Not inside the condition ";
                        echo "<br>";
                    }
                } else {
                    $inc = 0;
                    echo "***************<br>";
                    $first_in    = $obj->datetime;
                    $employee_id = $obj->ID;
                    $in          = $obj->datetime;
                    $tot         = [];
                    $day_log = 0;

                    EmployeeAttendance::where('employee_attendance_id', $log_in_pid)->update(['inout_status' => 2]);

                    $log_data->inout_status = 0;
                    $log_data->save();

                    $raw_data->status = 0;
                    $raw_data->save();
                    echo "Only in Log ";
                    echo "<br>";
                }
            }
            $inc++;
            echo "**********************<br> ";
            echo "<br>";
        }

        echo "<pre>";
        print_r($data_set);

        foreach ($data_set as $empKey => $employee) {
            $total_hours = [];
            foreach ($employee as $key => $data) {

                Log::info('emtered into foreach');

                $update_hour = DB::table('view_employee_in_out_data')
                    ->whereRaw("in_time_from > '" . $key . "' AND out_time_upto='" . $key . "'")
                    ->where('finger_print_id', $empKey)
                    ->first();

                if ($update_hour) {

                    Log::info('attendacne record');

                    $primary_key   = $update_hour->employee_attendance_id;
                    $total_hours[] = $update_hour->working_time; // have to check the correct update time
                    $total_hours[] = $data['hours'];
                    $workingHour   = $this->addhours($total_hours);
                    $workingTime   = $this->workingtime($update_hour->in_time, $update_hour->out_time);

                    ViewEmployeeInOutData::where('employee_attendance_id', $primary_key)->update([
                        'working_time' => $workingTime,
                        'working_hour' => $workingHour,
                        'out_time'     =>  $data['out_time'] == "" ? null : $data['out_time'],
                        'status'       => 1,
                    ]);
                } else {

                    Log::info('creating attendacne record');
                    $workingTime = $this->workingtime($key, $data['out_time']);
                    DB::table('view_employee_in_out_data')->insert([
                        'finger_print_id' => $empKey,
                        'date'            => date('Y-m-d', \strtotime($key)),
                        'in_time'         => $key,

                        'out_time'        =>  $data['out_time'],
                        'in_time_from'    => $data['in_time_from'],
                        'out_time_upto'   => $data['out_time_upto'],
                        'working_time'    => $workingTime,
                        'working_hour'    => $data['hours'],
                        'status'          => $data['hours'] >= 8 ? 1 : 0,

                    ]);
                }

                echo "Employee ID= " . $empKey . " |||  Date time=" . $key . " ||| Total Hours=" . $data['hours'] . "||| Outtime=" . $data['out_time'];
                echo "<br>";
            }

            // }
            echo "</pre>";
            echo "<br>";
        }
    }


    public function sample()
    {
        $date2 = Carbon::today()->subDay(1);
        $date = date('d', \strtotime($date2));
        dd($date);

        $lastLogRow = DB::table('ms_sql')->max('datetime');
        $date         = Carbon::now()->subDay(0)->format('Y-m-d');
        $time         = Carbon::today()->subDay(3)->subHour(6)->format('H:i:s');
        $array        = [4864, 4865, 4866, 4868, 4869, 4870, 4871, 4872];
        $carbon_parse = Carbon::parse($date)->format("Ym");
        $table_name = 't_lg' . $carbon_parse;
        dump($table_name);

        $LogCollections = DB::connection('sqlsrv')->table($table_name)
            // ->select('USRID', 'DEVUID', 'SRVDT', 'EVTLGUID')
            ->whereIn('DEVUID', [546280880, 546280864, 546280804, 546280802])
            ->where($table_name . '.EVT', 4102)
            ->where('USRID', 'like', '%FMS8802%')
            //->where('USRID', 'like', '%FMS8803%')
            ->where('SRVDT', '>=', '2022-07-13 00:00:00')
            ->orderBy('SRVDT', 'ASC')
            // ->groupBy('USRID', 'DEVUID', 'SRVDT', 'EVTLGUID')
            ->limit(2500)
            ->get();

        dd($LogCollections);
    }

    public function newEmployee()
    {

        $Employees = DB::connection('sqlsrv')->table('emp')->select('ID', 'Empname')->orderBy('Empname')->groupBy('ID', 'Empname')
            ->where('Empname', '!=', \null)->get();
        // dd($Employees);
        $tempArrayUser      = [];
        $tempArrayEmployee  = [];
        $totalDatasUser     = [];
        $totalDatasEmployee = [];
        foreach ($Employees as $employee) {
            // dd($employee);


            $tempArrayEmployee['finger_id']  = $employee->ID;
            $tempArrayEmployee['first_name'] = $employee->Empname;
            $tempArrayUser['user_name']      = $employee->Empname;
            $tempArrayUser['role_id']        = 3;
            $totalDatasUser[]                = $tempArrayUser;
            $totalDatasEmployee[]            = $tempArrayEmployee;
            $user_id                         = DB::table('user')->insertGetID([
                'user_name' => $employee->Empname,
                'role_id'   => 3,
            ]);
            $employee = DB::table('employee')->insert([
                'user_id'    => $user_id,
                'finger_id'  => $employee->ID,
                'first_name' => $employee->Empname,
            ]);
        }

        echo "<br>";
        echo "Success : Imported Successfully";
        echo "<br>";

        echo "<pre>";
        print_r($totalDatasUser);
        print_r($totalDatasEmployee);
        echo "<pre>";

        // return $totalDatas;

    }

    public function samsungNewEmployees()
    {

        Log::info("Controller is working fine!");
        $date         = Carbon::now()->subDay(0)->format('Y-m-d');
        $time         = Carbon::today()->subDay(3)->subHour(6)->format('H:i:s');
        $array        = [4864, 4865, 4866, 4868, 4869, 4870, 4871, 4872];
        $carbon_parse = Carbon::parse($date)->format("Ym");
        // $carbon_today = Carbon::parse($date)->format("Y-m-d");
        $table_name = 't_lg' . $carbon_parse;


        $all_fms_users = DB::connection('sqlsrv')->table('t_usr')->select('USRID', 'NM', 'USRUID')->where('USRID', 'like', '%FMS%')->orderby('USRUID')->groupby('USRID', 'NM', 'USRUID')->get();
        //dd($all_fms_users);
        // $Employees = DB::connection('sqlsrv')->table($table_name)->where('USRID', 'like', '%FMS%')->select('USRID', 'NM')->groupBy('USRID','NM')->get();
        //  dd($all_fms_users);
        $tempArrayUser      = [];
        $tempArrayEmployee  = [];
        $totalDatasUser     = [];
        $totalDatasEmployee = [];
        foreach ($all_fms_users  as $key => $employee) {
            $if_employee_exists = DB::table('employee')->where('finger_id', $employee->USRID)->first();

            if (!$if_employee_exists) {
                //dd($employee);
                $tempArrayEmployee['finger_id']  = $employee->USRID;
                $tempArrayEmployee['first_name'] = $employee->NM;
                $tempArrayUser['user_name']      = $employee->NM;
                $tempArrayUser['role_id']        = 3;
                $totalDatasUser[]                = $tempArrayUser;
                $totalDatasEmployee[]            = $tempArrayEmployee;
                $user_id                         = DB::table('user')->insertGetID([
                    'user_name' => $employee->NM,
                    'role_id'   => 3,
                ]);
                $employee = DB::table('employee')->insert([
                    'user_id'    => $user_id,
                    'finger_id'  => $employee->USRID,
                    'first_name' => $employee->NM,
                ]);
            }
        }

        echo "<br>";
        echo "Success : Imported Successfully";
        echo "<br>";



        echo "<pre>";
        print_r($totalDatasUser);
        print_r($totalDatasEmployee);
        echo "<pre>";

        // return $totalDatas;

    }

    public function newEmployee1()
    {

        $Employees = DB::connection('sqlsrv')->table('atteninfo')->select('ID', 'Empname')->orderBy('Empname')->groupBy('ID', 'Empname')
            ->where('Empname', '!=', \null)->limit(1)->get();
        // dd($Employees);
        $tempArrayUser      = [];
        $tempArrayEmployee  = [];
        $totalDatasUser     = [];
        $totalDatasEmployee = [];
        $count = 0;
        foreach ($Employees as $employee) {
            $if_employee_exists = DB::table('employee')->where('finger_id', $employee->ID)->first();
            $if_user_exists     = DB::table('user')->where('user_id', $employee->ID)->first();

            //dd($if_user_exists, $if_employee_exists);

            do {

                if ($if_user_exists && $if_employee_exists) {
                    $count++;
                }
                // dd($employee);
                $tempArrayEmployee['finger_id']  = $employee->ID;
                $tempArrayEmployee['first_name'] = $employee->Empname;
                $tempArrayUser['user_name']      = $employee->Empname;
                $tempArrayUser['role_id']        = 3;
                $totalDatasUser[]                = $tempArrayUser;
                $totalDatasEmployee[]            = $tempArrayEmployee;
                // $user_id                         = DB::table('user')->insertGetID([
                //     'user_name' => $employee->Empname,
                //     'role_id'   => 3,
                // ]);
                // $employee = DB::table('employee')->insert([
                //     'user_id'  => $user_id,
                //     'finger_id'  => $employee->ID,
                //     'first_name' => $employee->Empname,
                // ]);


            } while ($if_employee_exists && $if_user_exists);
        }

        echo "<br>";
        echo "Count : " . $count;
        echo "<br>";

        //  echo "<pre>";
        //  print_r($employee);
        //  print_r($if_employee_exists);
        //  print_r($if_user_exists);
        //  echo "<pre>";	

        //echo "<br>";
        //echo "Success : Imported Successfully";
        //echo "<br>";

        // echo "<pre>";
        // print_r($totalDatasUser);
        //print_r($totalDatasEmployee);
        //echo "<pre>";

        // return $totalDatas;

    }


    public function check_att()
    {

        $k = 0;
        $a = 0;
        $first_row = 0;
        $at = [];
        $bt = [];
        $attendance_data = [];
        $first_row_2 = 0;

        $results = DB::table('ms_sql')
            ->whereRaw("datetime >= '2022-07-12 00:00:00' AND datetime <= '2022-07-13 8:00:00'")
            ->where('ID', 'FMS8882')
            // ->where('status', 0)
            ->orderby('datetime', 'ASC')
            ->get();

        //  dd($results);

        foreach ($results as $key => $row) {

            // DB::table('ms_sql')->where('primary_id', $row->primary_id)->update(['status' => 1]);

            if ($first_row == 0 && $row->type == "OUT") {
                continue;
            } elseif (!isset($at[$k]['fromdate']) && $row->type == "OUT") {
                $j = $k;
                $j--;

                if (!isset($at[$j]['fromdate'])) {
                    continue;
                }
            } elseif (isset($at[$k]['fromdate']) && $row->type == "IN") {
                continue;
            }

            if ($row->type == "IN") {

                $j = $k;
                $j--;
                if ($k > 0) {
                    $j = $k;
                    $j--;

                    $datetime1 = new DateTime($at[$j]['todate']);
                    // print_r($datetime1);
                    $datetime2 = new DateTime($row->datetime);
                    //print_r($datetime2);
                    //echo "<br>";
                    $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                    $pieces = explode(":", $subtotal);
                    if ($pieces[0] > 5) {
                        if (isset($bt[$a]['fromdate'])) {
                            continue;
                        }
                        $bt[$a]['fromdate'] = $row->datetime;
                        $bt[$a]['statusin'] = $row->type;
                        $first_row_2 = 1;
                        //echo $pieces[0];
                        continue;
                    }
                }
                $at[$k]['fromdate'] = $row->datetime;
                $at[$k]['statusin'] = $row->type;
                $first_row = 1;
                continue;
            }

            if ($row->type == "OUT") {


                if ($first_row_2 == 0) {

                    if (isset($at[$k]['fromdate']) && $at[$k]['fromdate'] != "") {
                        // dump($row);
                        // dump($at);
                        $at[$k]['statusout'] = $row->type;
                        $at[$k]['todate'] = $row->datetime;
                        $datetime1 = new DateTime($at[$k]['fromdate']);
                        $datetime2 = new DateTime($row->datetime);
                        $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                        $at[$k]['subtotalhours'] = $subtotal;
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
                            $at[$j]['subtotalhours'] =  $subtotal;
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
                            $bt[$j]['subtotalhours'] =  $subtotal;
                            continue;
                        }
                    }
                }
            }
        }

        if (count($at) > 0) {
            if (!isset($at[count($at) - 1]['todate']))
                unset($at[count($at) - 1]);
        }

        if (count($bt) > 0) {
            if (!isset($bt[count($bt) - 1]['todate']))
                unset($bt[count($bt) - 1]);
        }


        for ($i = 0; $i < count($at); $i++) {
            $at[$i]['fromdate'] . "  -  " . $at[$i]['todate'] . "  ---  " . $at[$i]['subtotalhours'];
            "<br>";
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
            }
        }

        echo "<pre>";
        print_r($at);
        echo "<br>";
        print_r($bt);
        echo "</pre>";

        // Attendance data set return values...................................
        for ($i = 0; $i < count($at); $i++) {

            if ($i == 0) {
                $attendance_data['date'] = date('Y-m-d', strtotime($at[$i]['fromdate']));
                $attendance_data['in_time'] = $at[$i]['fromdate'];
                $attendance_data['finger_print_id'] = '';
            }

            $attendance_data['out_time'] = $at[count($at) - 1]['todate'];
            $attendance_data['working_time'] = $this->calculate_total_working_hours($at);
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
        }

        // return $attendance_data;

        echo "<pre>";
        print_r($attendance_data);
        echo "</pre>";
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
}
