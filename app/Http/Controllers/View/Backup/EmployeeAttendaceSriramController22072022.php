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

class EmployeeAttendaceController extends Controller
{


    public function fetchRawLog()
    {
        $time_start = microtime(true);
        Log::info("Controller is working fine!");
        $lastLogDateTime = DB::table('ms_sql')->max('datetime');
        $data = [];
        $bug = \null;
        dump($lastLogDateTime);

        if ($lastLogDateTime) {

            $LogCollections =  DB::connection('sqlsrv')->table('atteninfo')
                ->whereIn('deviceno', ['DS-K1T331W20210731V030230ENJ05119333', 'DS-K1T343MX20210914V030304ENG93163059', 'DS-K1T331W20210731V030230ENJ05119457', 'DS-K1T607PEF20211130V030232ENJ31948274'])
                ->select('ID', 'datetime', 'status', 'deviceno')
                //   ->orderBy('ID', 'ASC')
                ->orderBy('datetime', 'ASC')
                ->groupby('ID', 'datetime', 'status', 'deviceno')
                ->where('date', '>=', date('Y-m-d', \strtotime($lastLogDateTime)))
                ->limit(5000)
                ->get();
            // dd($LogCollections);

        } else {

            $LogCollections = DB::connection('sqlsrv')->table('atteninfo')
                ->whereIn('deviceno', ['DS-K1T331W20210731V030230ENJ05119333', 'DS-K1T343MX20210914V030304ENG93163059', 'DS-K1T331W20210731V030230ENJ05119457', 'DS-K1T607PEF20211130V030232ENJ31948274'])
                ->select('ID', 'datetime', 'status', 'deviceno')
                // ->orderBy('ID', 'ASC')
                ->orderBy('datetime', 'ASC')
                ->groupby('ID', 'datetime', 'status', 'deviceno')
                ->limit(5000)
                ->get();
            // dd($LogCollections);

        }

        foreach ($LogCollections as $key => $log) {

            $type         = \null;
            $time = Carbon::now();
            $check_record = MsSql::where('ID', $log->ID)->where('datetime', $log->datetime)->first();

            if (!$check_record) {

                if ($log->status == 'I') {
                    $type = "IN";
                } elseif ($log->status == 'IN') {
                    $type = "IN";
                } else {
                    $type = "OUT";
                }

                $data[] = [
                    'datetime'   => $log->datetime,
                    'ID'         => $log->ID,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'type'       => $type,
                ];
            }
        }

        try {
            DB::beginTransaction();
            MsSql::insert($data);
            DB::commit();
            $bug = 0;
        } catch (\Throwable $e) {
            DB::rollback();
            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";
        } finally {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        echo "<br>";
        echo '<b>Total Execution Time:</b> ' . ($execution_time) . 'Seconds';
        echo '<b>Total Execution Time:</b> ' . ($execution_time * 1000) . 'Milliseconds';
        echo "<br>";

        $bug == 0 ? $status = array('status' => 'Success') :  $status = array('status' => 'Failed');
        return $status;
    }

    public function attendance()
    {
        $data_format = [];
        $date2 = Carbon::today()->subDay(1);
        $day = date('d', \strtotime($date2));

        // dump($day);

        $day = 5;

        // $condition = 20;
        // for ($day = 5; $day <= $condition; $day++) {

        $start = sprintf("%02d", $day);
        $date = '2022-07' . '-' . $start . '';
        // dd($date);

        $start_date  = DATE('Y-m-d', strtotime($date)) . " 00:00:00";
        $end_date = DATE('Y-m-d', strtotime($date . " +1 day")) . " 08:00:00";

        // dd($start_date, $end_date);

        $employees = MsSql::select('ID')->whereBetween('datetime', [$start_date, $end_date])->groupby('ID')->get();

        foreach ($employees as $key1 => $finger_id) {

            $data_format =   $this->calculate_attendance($start_date, $end_date, $finger_id);

            if ($data_format != []) {
                $workingTime = explode(':', $data_format['working_time']);

                if ($workingTime[0] >= 1) {
                    $if_exists =     ViewEmployeeInOutData::where('finger_print_id', $data_format['finger_print_id'])->where('date', $data_format['date'])->first();

                    if (!$if_exists) {

                        echo 'created';
                        ViewEmployeeInOutData::insert($data_format);
                        echo "<pre>";
                        print_r($data_format);
                        echo "</pre>";
                    } else {
                        echo 'updated';
                        echo "<pre>";
                        print_r($data_format);
                        echo "</pre>";
                        ViewEmployeeInOutData::where('date', $data_format['date'])->where('finger_print_id', $data_format['finger_print_id'])->update($data_format);
                    }
                }
            }
        }
        //  }
    }



    public function calculate_attendance($date_from, $date_to, $finger_id)
    {

        $k = 0;
        $a = 0;
        $first_row = 0;
        $at = [];
        $bt = [];
        $first_row_2 = 0;
        $at_id = [];
        $bt_id = [];
        $attendance_data = [];

        $results = DB::table('ms_sql')
            ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
            ->where('ID', $finger_id->ID)
            ->where('status', 0)
            ->orderby('datetime', 'ASC')
            ->get();

        // dd($results);

        foreach ($results as $key => $row) {

            //   DB::table('ms_sql')->where('primary_id', $row->primary_id)->update(['status' => 1]);

            if ($first_row == 0 && $row->type == "OUT") {
                array_push($at_id, $row->primary_id);
                continue;
            } elseif (!isset($at[$k]['fromdate']) && $row->type == "OUT" && $first_row_2 == 0) {
                $j = $k;
                $j--;
                if (!isset($at[$j]['fromdate'])) {
                    array_push($at_id, $row->primary_id);
                    continue;
                }
            } elseif (isset($at[$k]['fromdate']) && $row->type == "IN" && $first_row_2 == 0) {

                $datetime1 = new DateTime($at[$k]['fromdate']);
                $datetime2 = new DateTime($row->datetime);
                $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                $pieces = explode(":", $subtotal);
                if ($pieces[0] > 9) {

                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);

                    $first_row_2 = 1;
                    continue;
                }

                array_push($at_id, $row->primary_id);

                continue;
            }

            if ($row->type == "IN") {
                $j = $k;
                $j--;
                if ($first_row_2 == 1) {
                    if (isset($bt[$a]['fromdate'])) {
                        array_push($at_id, $row->primary_id);
                        continue;
                    }
                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);

                    $first_row_2 = 1;
                    continue;
                }
                if ($k > 0) {
                    $j = $k;
                    $j--;

                    $datetime1 = new DateTime($at[$j]['todate']);
                    $datetime2 = new DateTime($row->datetime);
                    $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                    $pieces = explode(":", $subtotal);
                    if ($pieces[0] > 9) {
                        if (isset($bt[$a]['fromdate'])) {
                            array_push($bt_id, $row->primary_id);
                            continue;
                        }
                        $bt[$a]['fromdate'] = $row->datetime;
                        $bt[$a]['statusin'] = $row->type;
                        array_push($bt_id, $row->primary_id);

                        $first_row_2 = 1;
                        continue;
                    }
                }
                array_push($at_id, $row->primary_id);

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
                        if ($pieces[0] > 9) {
                            array_push($at_id, $row->primary_id);
                            continue;
                        }
                        $at[$k]['statusout'] = $row->type;
                        $at[$k]['todate'] = $row->datetime;
                        $at[$k]['subtotalhours'] = $subtotal;
                        array_push($at_id, $row->primary_id);

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
                            array_push($at_id, $row->primary_id);

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
                            array_push($bt_id, $row->primary_id);

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
                $at_id = $bt_id;
            }
        }

        for ($i = 0; $i <= count($at_id) - 1; $i++) {
            $sql = "update ms_sql3 set status=1 where primary_id=" . $at_id[$i];
            //echo "<br>".$sql."</br>";
            //$mysqli->query($sql);
        }
        echo "<br><pre>";
        print_r($at_id);
        echo "</br>";

        if (isset($at[0]['fromdate'])) {
            $currnet_date =  Carbon::createFromFormat('Y-m-d H:i:s', $at[0]['fromdate'])->format('Y-m-d');
            $from_date =  Carbon::createFromFormat('Y-m-d H:i:s', $date_from)->format('Y-m-d');
            if ($currnet_date !=  $from_date) {
                $k = 0;
                $a = 0;
                $first_row = 0;
                $at = [];
                $bt = [];
                $first_row_2 = 0;
                $at_id = [];
                $bt_id = [];
            }
        }

        $update_status = true;

        if (count($at) > 0) {
            foreach ($at_id as $primary_id) {
                echo 'Primary ID ' . $primary_id;
                $upd_to_date = $at[count($at) - 1]['todate'];
                $check_by_primary = MsSql::where('primary_id', $primary_id)->first();
                if ($update_status == true) {
                    $update =   DB::table('ms_sql')->where('primary_id', $primary_id)->update(['status' => 1]);
                    echo "<br>";
                    echo "Update status = " . $update_status;
                    echo "<br>";
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
                $attendance_data['finger_print_id'] = $finger_id->ID;
            }
            $attendance_data['out_time'] = $at[count($at) - 1]['todate'];
            $attendance_data['working_time'] = $this->calculate_total_working_hours($at);
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
        }

        echo "<pre>";
        print_r($at);
        // print_r($attendance_data);
        echo "</pre>";

        return $attendance_data;
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
            ->whereRaw("datetime >= '2022-07-10 05:00:00' AND datetime <= '2022-07-11 8:00:00'")
            ->where('ID', 'P003')
            // ->where('status', 0)
            ->orderby('datetime', 'ASC')
            ->get();

        // dd($results);

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
}
