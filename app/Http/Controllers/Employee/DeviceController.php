<?php

namespace App\Http\Controllers\Employee;

use App\User;
use Exception;
use App\Model\Device;
use App\Model\Employee;
use App\Model\MsSql;
use App\Components\Common;
use App\Model\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DateTime;

class DeviceController extends Controller
{

    //protected $employeeRepositories;

    /* public function __construct(EmployeeRepository $employeeRepositories){
    $this->employeeRepositories = $employeeRepositories;
    }
     */

    public function importemployee(Request $request){
        set_time_limit(0);
        return $this->import(30,$request);
        return redirect()->back()->with('success', 'Employee Details imported successfully.');
    }




    public function import($result,$request){
        set_time_limit(0);
        try {
            $check_device=Common::restartdevice();
            $check_device=json_decode($check_device);
            if($check_device->status=="all_offline_check_cable"){
                return redirect()->back()->with('error',$check_device->msg);
            }


            DB::beginTransaction();

            $emp_data = [];

            $device = Device::where('id', $request->device)->where('status', 1)->first();

            if ($device->device_status != 'online') {
                return redirect()->back()->with('error', 'Device currently offline!, Please try again.');
            }

            $rawdata = ["UserInfoSearchCond" => [
                "searchID"             => (string) random_int(23, 9999),
                "searchResultPosition" => ($result-30),
                "maxResults"           => $result,
            ],
            ];

            // dd(\json_encode($rawdata));

            $client   = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/AccessControl/UserInfo/Search', [
                'auth'  => [$device->username, $device->password, "digest"],
                'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                'json'  => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content    = $response->getBody()->getContents();
            $data       = json_decode($content);
            //dd($data);
            if($data->UserInfoSearch->numOfMatches==0){
                return redirect()->back()->with('success', 'Employee Details imported successfully.');
            }


            //dd($data->UserInfoSearch->UserInfo);
            //{"UserInfoOutList":{"UserInfoOut":[{"employeeNo":"","errorCode": ,"errorMsg": "","statusCode": ,"statusString":"","subStatusCode": ""},{"employeeNo":"","errorCode": ,"errorMsg": "","statusCode": ,"statusString":"","subStatusCode": ""}]}

            $emp_json = $data->UserInfoSearch->UserInfo;
            foreach ($emp_json as $key => $Data) {
                $emp         = [];
                $check_exist = Employee::whereRaw('device_employee_id="' . $Data->employeeNo . '" OR finger_id="' . $Data->employeeNo . '"')->first();
                if (!$check_exist) {
                    $emp['role_id']            = 3;
                    $emp['user_name']          = $employee_no          = $Data->name;
                    $emp['device_employee_id'] = $employee_no = $Data->employeeNo;
                    $emp['status']             = 1;
                   // $emp['created_by']         = Auth::user()->user_id;
                    //$emp['updated_by']         = Auth::user()->user_id;
                    $parentData                = User::create($emp);
                    $emp_data[]                = ['user_id' => $parentData->user_id, 'employee_no' => $Data->employeeNo, 'name' => $Data->name];
                } else {
                    $emp_data[] = ['user_id' => $check_exist->user_id, 'employee_no' => $Data->employeeNo, 'name' => $Data->name];
                }
            }

            //dd($emp_data);

            if (count($emp_data)) {
                $i = 0;
                foreach ($emp_data as $Data) {
                    $i++;
                    $rawdata = [
                        "FaceInfoSearchCond" => [
                            "searchID"             => (string) random_int(23, 9999),
                            "searchResultPosition" => 0,
                            "maxResults"           => 100,
                            "employeeNo"           => (string) $Data['employee_no'],
                            "faceLibType"          => "blackFD",
                        ],
                    ];

                    $client   = new \GuzzleHttp\Client();
                    $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/Intelligent/FDLib/FDSearch', [
                        'auth'  => [$device->username, $device->password, "digest"],
                        'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        'json'  => $rawdata,
                    ]);

                    $statusCode = $response->getStatusCode();
                    $content    = $response->getBody()->getContents();
                    $data       = json_decode($content);
                    $face_data='';
                    if(isset($data->FaceInfoSearch) && isset($data->FaceInfoSearch->FaceInfo[0]))
                        $face_data  = $data->FaceInfoSearch->FaceInfo[0];

                    $imgName = "";

                    if (isset($face_data->faceURL) && $face_data->faceURL) {
                        $face_path = explode("/HikGatewayStorage", $face_data->faceURL);
                        $face_path = $face_path[1];

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('GET', $face_path, [
                            'headers' => ['Accept-Encoding' => 'gzip, deflate, br'],
                            'auth'    => [$device->username, $device->password, "digest"],
                            // 'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        ]);

                        $imgName  = $i . random_int(1, 10000) . date('Y_m_d_H_i_s') . '.png';
                        $pic_path = 'uploads/employeePhoto/' . $imgName;
                        file_put_contents($pic_path, $response->getBody()->getContents());
                    }

                    $check_employee = Employee::whereRaw('device_employee_id="' . $Data['employee_no'] . '" OR finger_id="' . $Data['employee_no'] . '"')->first();
                    if (!$check_employee) {
                        $employeeData['first_name']         = $Data['name'];
                        $employeeData['user_id']            = $Data['user_id'];
                        $employeeData['photo']              = $imgName;
                        $employeeData['device_employee_id'] = $Data['employee_no'];
                        $employeeData['finger_id']          = $Data['employee_no'];
                        $employee_qry                       = Employee::create($employeeData);
                        $employeeID                         = $employee_qry->employee_id;
                    } else {
                        if ($imgName) {
                            $check_employee->photo = $imgName;
                            $check_employee->save();
                        }
                        $employeeID                         = $check_employee->employee_id;
                        $check_employee->device_employee_id = $Data['employee_no'];
                        $check_employee->save();

                    }

                    $access_chk = AccessControl::where('device_employee_id', $Data['employee_no'])->where('device', $request->device)->first();
                    if (!$access_chk) {
                        $acc_ins                     = new AccessControl;
                        $acc_ins->employee           = $employeeID;
                        $acc_ins->device             = $device->id;
                        $acc_ins->status             = 1;
                        $acc_ins->device_employee_id = $Data['employee_no'];
                        $acc_ins->save();
                    }

                }
            }
            DB::commit();
            return $this->import($result+30,$request);
        }catch (Exception $e) {
            return redirect()->back()->with('error',Common::errormsg());
            // dd($e);
        }
    }


    public function testlog(Request $request){
	
	//\Log::info(print_r($request->all(),1));

	$myfile = fopen("logdata.txt", "a+") or die("Unable to open file!");
        $txt = print_r($_REQUEST,1);
        fwrite($myfile, $txt);
        $txt = "OUT PUNCH.".DATE('d-m-Y h:i:s A').".\n";
        fwrite($myfile, $txt);
	fclose($myfile);

        $eventLog=$_REQUEST['event_log'];
        $eventLog=json_decode($eventLog); 
        
        if (isset($eventLog->AccessControllerEvent->employeeNoString)) { 
            //$device=Device::where('name',$eventLog->AccessControllerEvent->deviceName)->first();
            
	    $device=Device::where('id',1)->first();
	    $time=$eventLog->dateTime;

            $log           = MsSql::where('ID', $eventLog->AccessControllerEvent->employeeNoString)->where('device', $device->id)->where('datetime', DATE('Y-m-d H:i:s', strtotime($time)))->first();
            $last_record   = MsSql::where('ID', $eventLog->AccessControllerEvent->employeeNoString)->where('device', $device->id)->orderBy('datetime', 'Desc')->first();
            $employee_data = Employee::where('device_employee_id', $eventLog->AccessControllerEvent->employeeNoString)->first();

            if (!$log && $employee_data) {
                $log_insert                     = new MsSql();
                $log_insert->ID                 = $employee_data->finger_id;
                $log_insert->employee           = $employee_data->employee_id;
                $log_insert->device             = $device->id;
                $log_insert->device_employee_id = $eventLog->AccessControllerEvent->employeeNoString;
                $log_insert->status             = 0;

                if (isset($last_record)) {
                    $last_datetime        = new DateTime($last_record->datetime);
                    $current_log_datetime = new Datetime(DATE('Y-m-d H:i:s', strtotime($time)));
                    $diff                 = $last_datetime->diff($current_log_datetime);
                    // dd($diff->h);
                }

                if ($last_record && (date('Y-m-d', strtotime($last_record->datetime)) != date('Y-m-d', strtotime($time)))) {
                    $log_insert->type = 'IN';
                } elseif ($last_record && $last_record->type == 'IN') {
                    $log_insert->type = 'OUT';
                } else {
                    $log_insert->type = 'IN';
                }

                $log_insert->datetime = DATE('Y-m-d H:i:s', strtotime($time));
                $log_insert->save();

                $ltime     = DATE('h:i A', strtotime($time));
                $ldate     = DATE('d-m-Y', strtotime($time));
                $phone     = $employee_data->phone;
                $firstname = $employee_data->first_name;
            }
            
        }
        

        return \response()->json([
            'message' => 'success',
        ], 200);
    }
}
