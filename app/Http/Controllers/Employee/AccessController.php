<?php

namespace App\Http\Controllers\Employee;

use Exception;
use App\Model\Device;
use App\Model\Employee;
use App\Model\Cron;
use App\Components\Common;
use App\Model\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Model\EmployeeAttendance;
use App\Model\MsSql;
use DateTime;

class AccessController extends Controller
{

    public function index(Request $request)
    {

        $results       = MsSql::orderBy('datetime', 'desc')->paginate(10);
        $last_sync_qry = MsSql::orderBy('datetime', 'DESC')->first();

        $last_sync = "";
        if ($last_sync_qry) {
            $last_sync = DATE('d-m-Y h:i A', strtotime($last_sync_qry->datetime));
        }

        if (request()->ajax()) {
            /* if($request->role_id !='') {
            $results = Employee::whereHas('userName', function($q) use($request){
            $q->with('role')->where('role_id', $request->role_id);
            })->with('department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries')->orderBy('employee_id', 'DESC');
            }else{
            $results = Employee::with(['userName' => function ($q) {
            $q->with('role');
            }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])->orderBy('employee_id', 'DESC');
            }

            if($request->department_id !=''){
            $results->where('department_id',$request->department_id);
            }

            if($request->designation_id !=''){
            $results->where('designation_id',$request->designation_id);
            }

            if($request->employee_name !=''){
            $results->where(function($query) use ($request) {
            $query->where('first_name', 'like','%' . $request->employee_name . '%')
            ->orWhere('last_name', 'like','%' . $request->employee_name . '%');
            });
            }*/
            $results = MsSql::orderBy('datetime', 'desc');
            $results = $results->paginate(10);
            return View('admin.employee.access.pagination', ['results' => $results])->render();
        }

        return view('admin.employee.access.index', ['results' => $results, 'lastsync' => $last_sync]);
    }

    public function edit($id)
    {
        $device   = Device::findOrFail($id);
        $employee = Employee::where('photo', '!=', '')->get();
        return view('admin.employee.access.form', ['device' => $device, 'employee' => $employee]);
    }

    public function store(Request $request)
    {

        try {

            /*if(!isset($request->skip))
            Common::triggerException();*/

            $check_device = Common::restartdevice();
            $check_device = json_decode($check_device);
            if ($check_device->status == "all_offline_check_cable") {
                return redirect()->back()->with('error', $check_device->msg);
            }

            DB::beginTransaction();

            $device = Device::findOrFail($request->device_id);

            if ($device->device_status != 'online') {
                return redirect()->back()->with('error', 'Device currently offline!, Please try again.');
            }

            $acc_cont = AccessControl::where('device', $request->device_id)->get();

            $exist_emp = [];
            if ($acc_cont && count($acc_cont)) {
                foreach ($acc_cont as $control) {
                    $exist_emp[] = $control->device_employee_id;
                }
                $remove = [];
                if (is_null($request->emp_id)) {
                    foreach ($exist_emp as $remove_emp) {
                        $remove[] = ['employeeNo' => (string) $remove_emp];
                    }
                    $unsel_emp = $exist_emp;
                } else {
                    $unsel_emp = array_diff($exist_emp, $request->emp_id);
                    foreach ($unsel_emp as $remove_emp) {
                        $remove[] = ['employeeNo' => (string) $remove_emp];
                    }
                }

                //dd(json_encode($remove));

                if (count($remove)) {

                    $rawdata = [
                        "UserInfoDetail" => [
                            "mode"           => "byEmployeeNo",
                            "EmployeeNoList" =>
                            $remove,

                        ],
                    ];

                    //dd(json_encode($rawdata));

                    $client   = new \GuzzleHttp\Client();
                    $response = $client->request('PUT', 'http://localhost:' . $device->port . '/' . $device->protocol . '/AccessControl/UserInfoDetail/Delete', [
                        'auth'  => [$device->username, $device->password, "digest"],
                        'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        'json'  => $rawdata,
                    ]);

                    $statusCode = $response->getStatusCode();
                    $content    = $response->getBody()->getContents();
                    $data       = json_decode($content);

                    //dd($data);

                    $rawdata = [
                        "FaceInfoDelCond" => [
                            "EmployeeNoList" =>
                            $remove,
                        ],
                    ];

                    //dd(json_encode($rawdata));

                    $client   = new \GuzzleHttp\Client();
                    $response = $client->request('PUT', 'http://localhost:' . $device->port . '/' . $device->protocol . '/Intelligent/FDLib/FDSearch/Delete', [
                        'auth'  => [$device->username, $device->password, "digest"],
                        'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        'json'  => $rawdata,
                    ]);

                    AccessControl::whereIn('device_employee_id', $unsel_emp)->where('device', $device->id)->delete();
                }
            }

            //dd($request->emp_id);

            $empinfo  = [];
            $facedata = [];
            if (!is_null($request->emp_id)) {
                foreach ($request->emp_id as $key => $emp_id) {
                    $access   = AccessControl::where('device_employee_id', $emp_id)->where('device', $request->device_id)->first();
                    $employee = Employee::where('device_employee_id', $emp_id)->first();
                    if (!$access) {
                        $acc_ins                     = new AccessControl;
                        $acc_ins->employee           = $employee->employee_id;
                        $acc_ins->device             = $request->device_id;
                        $acc_ins->status             = 1;
                        $acc_ins->device_employee_id = $emp_id;
                        $acc_ins->save();

                        $empinfo[]  = ["employeeNo" => $emp_id, "name" => $employee->username->user_name, 'Valid' => ["beginTime" => "2017-01-01T00:00:00", "endTime" => "2027-12-31T23:59:59"]];
                        $facedata[] = ["employeeNo" => $emp_id, "name" => $employee->username->user_name, 'photo' => $employee->photo];
                    }
                }
            }

            //dd($empinfo);

            if (count($empinfo)) {

                $rawdata = [
                    "UserInfo" =>
                    $empinfo,

                ];

                $client   = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/AccessControl/UserInfo/Record', [
                    'auth'  => [$device->username, $device->password, "digest"],
                    'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                    'json'  => $rawdata,
                ]);

                $statusCode = $response->getStatusCode();
                $content    = $response->getBody()->getContents();
                $data       = json_decode($content);

                //dd($data);

                if (isset($data->UserInfoOutList->UserInfoOut[0]->errorMsg) && $data->UserInfoOutList->UserInfoOut[0]->errorMsg == "employeeNoAlreadyExist") {
                    //dd($data);
                } else {
                    foreach ($facedata as $face_data) {

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/Intelligent/FDLib/FaceDataRecord', [
                            'auth'      => [$device->username, $device->password, "digest"],
                            'query'     => ['format' => 'json', 'devIndex' => $device->devIndex],
                            'multipart' => [
                                [
                                    'name'     => 'facedatarecord',
                                    'contents' => json_encode(["FaceInfo" => ["employeeNo" => (string) $face_data['employeeNo'], "faceLibType" => "blackFD"]]),
                                ],
                                [
                                    'name'     => 'faceimage',
                                    'contents' => file_get_contents(URL::asset('uploads/employeePhoto/' . $face_data['photo'])),
                                    'filename' => $face_data['photo'],
                                ],
                            ],
                        ]);
                        /*$statusCode = $response->getStatusCode();
                    $content = $response->getBody()->getContents();
                    $data=json_decode($content);
                    dd($data);*/
                    }
                }
            }

            $bug = 0;
            DB::commit();
            //dd($empinfo);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 1;
            //dd($e);
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'employee allow access successfully saved.');
        } else {
            return redirect()->back()->with('error', Common::errormsg());
        }
    }

    public function cloneform($id)
    {
        $device         = Device::findOrFail($id);
        $linked_devices = AccessControl::groupBy('device')->get();
        $list           = [];
        foreach ($linked_devices as $Device) {
            $list[] = $Device->device;
        }
        $map_device = Device::whereNotIn('id', $list)->where('verification_status', '=', 1)->where('status', '!=', 2)->get();
        return view('admin.employee.access.cloneform', ['device' => $device, 'map_device' => $map_device]);
    }

    function clone(Request $request)
    {

        $devList = $request->devicelist;

        try {

            /*if(!isset($request->skip))
            Common::triggerException();*/

            $check_device = Common::restartdevice();
            $check_device = json_decode($check_device);
            if ($check_device->status == "all_offline_check_cable") {
                return redirect()->back()->with('error', $check_device->msg);
            }

            DB::beginTransaction();

            //$request->device_id=2;
            // $access_qry = AccessControl::where('device', $request->device_id)->limit(2)->get();
            foreach ($request->devicelist as $deviceData) {

                $clone_device = Device::findOrFail($deviceData);

                AccessControl::where('device', $request->device_id)->chunk(2, function ($access_qry) use ($request, $clone_device) {

                    $empinfo  = [];
                    $facedata = [];

                    foreach ($access_qry as $key => $Data) {
                        $emp_id   = $Data->employee;
                        $access   = AccessControl::where('employee', $emp_id)->where('device', $clone_device->id)->first();
                        $employee = Employee::findOrFail($emp_id);
                        if (!$access) {
                            $acc_ins                     = new AccessControl;
                            $acc_ins->employee           = $emp_id;
                            $acc_ins->device             = $clone_device->id;
                            $acc_ins->status             = 1;
                            $acc_ins->device_employee_id = $Data->device_employee_id;
                            $acc_ins->save();

                            $empinfo[]  = ["employeeNo" => (string) $Data->device_employee_id, "name" => $employee->username->user_name, 'Valid' => ["beginTime" => "2017-01-01T00:00:00", "endTime" => "2027-12-31T23:59:59"]];
                            $facedata[] = ["employeeNo" => (string) $Data->device_employee_id, "name" => $employee->username->user_name, 'photo' => $employee->photo];
                        }
                    }

                    //dd(json_encode($empinfo));

                    if (count($empinfo)) {

                        $rawdata = [
                            "UserInfo" =>
                            $empinfo,

                        ];

                        //dd(json_encode($rawdata));

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'http://localhost:' . $clone_device->port . '/' . $clone_device->protocol . '/AccessControl/UserInfo/Record', [
                            'auth'  => [$clone_device->username, $clone_device->password, "digest"],
                            'query' => ['format' => 'json', 'devIndex' => $clone_device->devIndex],
                            'json'  => $rawdata,
                        ]);

                        $statusCode = $response->getStatusCode();
                        $content    = $response->getBody()->getContents();
                        $data       = json_decode($content);

                        foreach ($facedata as $face_data) {

                            $client   = new \GuzzleHttp\Client();
                            $response = $client->request('POST', 'http://localhost:' . $clone_device->port . '/' . $clone_device->protocol . '/Intelligent/FDLib/FaceDataRecord', [
                                'auth'      => [$clone_device->username, $clone_device->password, "digest"],
                                'query'     => ['format' => 'json', 'devIndex' => $clone_device->devIndex],
                                'multipart' => [
                                    [
                                        'name'     => 'facedatarecord',
                                        'contents' => json_encode(["FaceInfo" => ["employeeNo" => (string) $face_data['employeeNo'], "faceLibType" => "blackFD"]]),
                                    ],
                                    [
                                        'name'     => 'faceimage',
                                        'contents' => file_get_contents(URL::asset('uploads/employeePhoto/' . $face_data['photo'])),
                                        'filename' => $face_data['photo'],
                                    ],
                                ],
                            ]);
                            /*$statusCode = $response->getStatusCode();
                        $content = $response->getBody()->getContents();
                        $data=json_decode($content);
                        dd($data);*/
                        }
                    }
                });
            }

            DB::commit();

            return redirect()->back()->with('success', 'Device details successfully cloned.');
        } catch (Exception $e) {
            DB::rollback();
            //$error=$e->getMessage();
            return redirect()->back()->with('error', Common::errormsg());
        }
    }
    public function log(Request $request)
    {

        //dd(date("Y-m-d\TH:i:sP"));

        //$uri = Route::getFacadeRoot()->current()->uri();
        $bug = 1;
        set_time_limit(0);
        try {

            Log::info('Cron Start Time : ' . DATE('d-m-Y h:i A'));

            $check_device = Common::restartdevice();
            $check_device = json_decode($check_device);
            if ($check_device->status == "all_offline_check_cable") {
                return redirect()->back()->with('error', $check_device->msg);
            }

            DB::beginTransaction();

            $access_control=AccessControl::orderBy('device', 'ASC')->orderBy('device_employee_id','ASC')->get();

                foreach ($access_control as $control) {
                    Log::info("EMP ID==".$control->device_employee_id);
                    $attendance_log = MsSql::where('device', $control->device)->orderBy('datetime', 'DESC')->first();

                    if ($attendance_log) {
                        $start_datetime = date("Y-m-d\TH:i:sP", \strtotime('-23 hours', \strtotime($attendance_log->datetime)));
                    } else {
                        $start_datetime = date("Y-m-d\TH:i:sP", \strtotime("-36 hours"));
                    }



                   // Log::info('Start Date Time : ' . $start_datetime);

                    //dd($start_datetime);

                    //$start_datetime = date("Y-m-d\TH:i:sP", \strtotime("2023-01-01 12:00:00"));

                    Log::info('Start Date Time : ' . $start_datetime);

                    //dd($start_datetime);

                    // $start_datetime = date("Y-m-d\TH:i:sP", \strtotime("2022-08-21 00:00:01"));
                    // $end_datetime = date("Y-m-d\TH:i:sP", \strtotime("2022-08-16 00:00:00"));

                    // $start_datetime = date("Y-m-d\TH:i:sP", \strtotime("-10 hours"));

                    $device = Device::where('id', $control->device)->where('device_status', 'online')->where('status', '!=', 2)->first();
                    // Log::info(print_r($device)); 
                    if ($device) {
                        $rawdata = [
                            "AcsEventSearchDescription" => [
                                "searchID"             => (string) random_int(0, 100),
                                "searchResultPosition" => 0,
                                "maxResults"           => 1000,
                                "AcsEventFilter"       => [
                                    "employeeNo" => (string) $control->device_employee_id,
                                    "startTime"  => $start_datetime,
                                    // "endTime"    => $end_datetime,
                                ],
                            ],
                        ];

                        //dd(json_encode($rawdata));

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/AccessControl/AcsEvent', [
                            'auth'  => [$device->username, $device->password, "digest"],
                            'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                            'json'  => $rawdata,
                        ]);

                        $statusCode = $response->getStatusCode();
                        $content    = $response->getBody()->getContents();
                        $data       = json_decode($content);
                        //\Log::info(print_r($data,1).print_r($rawdata,1));
                        //dd($data->AcsEventSearchResult->MatchList);

                        if (isset($data->AcsEventSearchResult->MatchList)) {
                            $emp_data = $data->AcsEventSearchResult->MatchList;
                            //Log::info(print_r($emp_data,1));

                            foreach ($emp_data as $Data) {

                                if (isset($Data->employeeNoString)) { //&& isset($Data->attendanceStatus)
                                    
                                    $log           = MsSql::where('ID', $Data->employeeNoString)->where('device', $device->id)->where('datetime', DATE('Y-m-d H:i:s', strtotime($Data->time)))->first();
                                    $last_record   = MsSql::where('ID', $Data->employeeNoString)->where('device', $device->id)->orderBy('datetime', 'Desc')->first();
                                    $employee_data = Employee::where('device_employee_id', $Data->employeeNoString)->first();

                                    if (!$log && $employee_data) {
                                        $log_insert                     = new MsSql();
                                        $log_insert->ID                 = $employee_data->finger_id;
                                        $log_insert->employee           = $employee_data->employee_id;
                                        $log_insert->device             = $device->id;
                                        $log_insert->device_employee_id = $Data->employeeNoString;
                                        $log_insert->status             = 0;

                                        if (isset($last_record)) {
                                            $last_datetime        = new DateTime($last_record->datetime);
                                            $current_log_datetime = new Datetime(DATE('Y-m-d H:i:s', strtotime($Data->time)));
                                            $diff                 = $last_datetime->diff($current_log_datetime);
                                            // dd($diff->h);
                                        }

                                        if ($last_record && (date('Y-m-d', strtotime($last_record->datetime)) != date('Y-m-d', strtotime($Data->time)))) {
                                            $log_insert->type = 'IN';
                                        } elseif ($last_record && $last_record->type == 'IN') {
                                            $log_insert->type = 'OUT';
                                        } else {
                                            $log_insert->type = 'IN';
                                        }

                                        $log_insert->datetime = DATE('Y-m-d H:i:s', strtotime($Data->time));
                                        $log_insert->save();

                                        $ltime     = DATE('h:i A', strtotime($Data->time));
                                        $ldate     = DATE('d-m-Y', strtotime($Data->time));
                                        $phone     = $employee_data->phone;
                                        $firstname = $employee_data->first_name;

                                        /*if($phone){
                                        $client = new \GuzzleHttp\Client();
                                        $response = $client->request('GET','https://sms.messagewall.in/api/v2/sms/send', [
                                        'query'=>['access_token'=>'b70a509da9c88b0ca98cdf855d1d5e4c','to'=>$phone,'message'=>'Your kid '.$firstname.' has boarded the school bus at '.$ltime.' on '.$ldate.' Onmessage','service'=>'T','sender'=>'ONMSGG','template_id'=>'1707165958782884789']
                                        ]); //8015102046

                                        $statusCode=$response->getStatusCode();
                                        $content=$response->getBody()->getContents();
                                        $log_insert->sms_log=$content;
                                        $log_insert->save();
                                        }*/

                                        //$json=json_decode($content);

                                    }
                                }
                            }
                        }
                    }
                }
            

            DB::commit();

            Log::info('Cron End Time : ' . DATE('d-m-Y h:i A'));
            if ($request->redirect) {
                $bug = 0;

                if (isset($check_device->offline_device) && $check_device->offline_device) {
                    return redirect()->back()->with('error', $check_device->msg);
                } else {
                    return redirect()->back()->with('success', 'Attendance Log Sync Successfully !');
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            dd($error);
            return redirect()->back()->with('error', Common::errormsg());
            //dd($error);
            /*if(strpos($error,'Internal error') !== FALSE){
            Common::clearinternalerror();

            sleep(5);
            $req=new \Illuminate\Http\Request();
            $req->skip=1;
            $req->redirect=1;
            return $this->log($req);
            }*/
            //return redirect()->back()->with('error', $error);
        }
    }

    public function manualsync(Request $request){

        $cron = Cron::where('status', 0)->first();

        if(empty($cron)){
            $cron        = new Cron;
            $cron->type  = 1;
            $cron->status = 0; 
            $cron->save();
            return redirect()->back()->with('success','Request has been successfully placed..will start soon !');           
        }else{
            return redirect()->back()->with('error', "Request already exists / pending "); 
        }
    }


    public function push_into_sqlserver()
    {
        $log_data = [];
        $message  = '';
        $bug      = null;

        set_time_limit(0);
        $last_Log = DB::connection('sqlsrv')->table('attlog')->orderByDesc('authDateTime')->first();

        if (isset($last_Log)) {
            $new_log = DB::table('employee_attendance')->where('in_out_time', '>=', \strtotime('-24 hours', \strtotime($last_Log->authDateTime)))
                ->join('device', 'device.id', '=', 'employee_attendance.device')
                ->join('employee', 'employee.finger_id', '=', 'employee_attendance.finger_print_id')
                ->select('employee_attendance.*', 'device.name', 'device.model', 'employee.first_name')->orderBy('employee_attendance.in_out_time', 'asc')->get();
        } else {
            $new_log = DB::table('employee_attendance')->join('device', 'device.id', '=', 'employee_attendance.device')
                ->join('employee', 'employee.finger_id', '=', 'employee_attendance.finger_print_id')
                ->select('employee_attendance.*', 'device.name', 'device.model', 'employee.first_name')->orderBy('employee_attendance.in_out_time', 'asc')->get();
        }


        DB::connection('sqlsrv')->unprepared('SET IDENTITY_INSERT attlog OFF');

        foreach ($new_log as $key => $value) {

            $check_duplicate = DB::connection('sqlsrv')->table('attlog')->where('employeeID', $value->finger_print_id)->where('authDateTime', $value->in_out_time)->first();

            if (!$check_duplicate) {

                $result = DB::connection('sqlsrv')->table('attlog')->insert([
                    'employeeID'   => $value->finger_print_id,
                    'authDateTime' => $value->in_out_time,
                    'authDate'     => date('Y-m-d'),
                    'authTime'     => date('H:i:s'),
                    'direction'    => $value->type,
                    'deviceName'   => $value->name,
                    'deviceSN'     => $value->model,
                    'personName'   => $value->first_name,
                    // 'cardNo'       => '',
                    // 'serialNo'     => $value->primary_id,
                ]);

                $message = 'Attendacne log successfully pushed into sql server';
            } else {
                $message = 'Attendacne log already exists! or There are no new records found!';
            }
        }

        DB::connection('sqlsrv')->unprepared('SET IDENTITY_INSERT attlog ON');

        echo "<br>";
        echo $message;
        echo "</br>";

        return \redirect()->back()->with('success', $message);
    }
    public function manual(Request $request)
    {
        // if ($_POST) {
        //$date = $request->date;
        //$crons = MsSql::where('datetime',$date)->get();  
        // }
        // $crons = Cron::orderBy('created_at', 'desc')->get();           
        return View('admin.employee.access.manual' );
    }  
    public function cronstatus(Request $request)
    {
                 $crons = Cron::orderBy('created_at', 'desc')->get();           
        return View('admin.employee.access.cronstatus', ['crons' => $crons]);
    }
     
}
