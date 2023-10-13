<?php

namespace App\Console\Commands;

use App\Components\Common;
use App\Model\EmployeeInOutData;
use App\Model\LastRecordID;
use App\Model\Employee;
use App\User;
use Illuminate\Console\Command;

class ExportEmployee extends Command
{

    protected $signature   = 'employee:export';
    protected $description = 'Export Attendance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

	return true;

        \DB::beginTransaction();

        $client   = new \GuzzleHttp\Client();
        $response = $client->request('GET', Common::liveurl() . "employeecount");
        $json=$response->getBody()->getContents();
        
        $json=json_decode($json);
        if(isset($json->data->employee_id))
            $lastID=$json->data->employee_id;
        else
            $lastID=0;

         $skip=1;
         $local_empID=Employee::orderBy('employee_id','DESC')->first();
         if($local_empID->employee_id==$lastID){
            $skip=0;
         }

            
         if($skip){

                Employee::where('employee_id', '>', $lastID)->chunk(3, function ($employee) {

                    foreach ($employee as $Data) {

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('POST', Common::liveurl() . "syncemployee", [
                            'form_params' => [
                                'employee_id'=>$Data->employee_id,
                                'user_id'=>$Data->user_id,
                                'finger_id'=>$Data->finger_id,
                                'department_id'=>$Data->department_id,
                                'designation_id'=>$Data->designation_id,
                                'branch_id'=>$Data->branch_id,
                                'supervisor_id'=>$Data->supervisor_id,
                                'work_shift_id'=>$Data->work_shift_id,
                                'esi_card_number'=>$Data->esi_card_number,
                                'pf_account_number'=>$Data->pf_account_number,
                                'pay_grade_id'=>$Data->pay_grade_id,
                                'hourly_salaries_id'=>$Data->hourly_salaries_id,
                                'email'=>$Data->email,
                                'first_name'=>$Data->first_name,
                                'last_name'=>$Data->last_name,
                                'date_of_birth'=>$Data->date_of_birth,
                                'date_of_joining'=>$Data->date_of_joining,
                                'date_of_leaving'=>$Data->date_of_leaving,
                                'gender'=>$Data->gender,
                                'religion'=>$Data->religion,
                                'marital_status'=>$Data->marital_status,
                                'photo'=>$Data->photo,
                                'address'=>$Data->address,
                                'emergency_contacts'=>$Data->emergency_contacts,
                                'document_title'=>$Data->document_title,
                                'document_name'=>$Data->document_name,
                                'document_expiry'=>$Data->document_expiry,
                                'document_title2'=>$Data->document_title2,
                                'document_name2'=>$Data->document_name2,
                                'document_expiry2'=>$Data->document_expiry2,
                                'document_title3'=>$Data->document_title3,
                                'document_name3'=>$Data->document_name3,
                                'document_expiry3'=>$Data->document_expiry3,
                                'document_title4'=>$Data->document_title4,
                                'document_name4'=>$Data->document_name4,
                                'document_expiry4'=>$Data->document_expiry4,
                                'document_title5'=>$Data->document_title5,
                                'document_name5'=>$Data->document_name5,
                                'document_expiry5'=>$Data->document_expiry5,
                                'phone'=>$Data->phone,
                                'status'=>$Data->status,
                                'permanent_status'=>$Data->permanent_status,
                                'created_by'=>$Data->created_by,
                                'updated_by'=>$Data->updated_by,
                                'deleted_at'=>$Data->deleted_at,
                                'created_at'=>$Data->created_at,
                                'updated_at'=>$Data->updated_at,
                                'device_employee_id'=>$Data->device_employee_id,
                            ]
                        ]);
                    }
                });
            }



            $client   = new \GuzzleHttp\Client();
            $response = $client->request('GET', Common::liveurl() . "usercount");
            $json=$response->getBody()->getContents();
            
            $json=json_decode($json);
            if(isset($json->data->user_id))
                $lastID=$json->data->user_id;
            else
                $lastID=0;

             $skip=1;
             $local_userID=User::orderBy('user_id','DESC')->first();
             if($local_userID->user_id==$lastID){
                $skip=0;
             }
                
             if($skip){

                    User::where('user_id', '>', $lastID)->chunk(2, function ($user) {

                    foreach ($user as $Data) {

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('POST', Common::liveurl() . "syncuser", [
                            'form_params' => [
                                'user_id'=>$Data->user_id,
                                'role_id'=>$Data->role_id,
                                'user_name'=>$Data->user_name,
                                'password'=>(is_null($Data->password) || !$Data->password) ? NULL : $Data->password,
                                'status'=>$Data->status,
                                'remember_token'=>$Data->remember_token,
                                'created_by'=>$Data->created_by,
                                'updated_by'=>$Data->updated_by,
                                'deleted_at'=>$Data->deleted_at,
                                'created_at'=>$Data->created_at,
                                'updated_at'=>$Data->updated_at,
                                'device_employee_id'=>$Data->device_employee_id,
                            ]
                        ]);
                    }
                });
             }

            \DB::commit();

            return true;

      /*  $last_push = EmployeeInOutData::where('live_status', 1)->orderBy('employee_attendance_id', 'DESC')->first();
        if ($last_push) {
            $last_record->attendance_id = $last_push->employee_attendance_id;
            $last_record->save();
        }*/

    }
}
