<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobApplicationRequest;
use App\Lib\Enumerations\JobStatus;
use App\Model\Job;
use App\Model\JobApplicant;
use App\Model\Services;
use App\Model\MsSql;
use App\Model\EmployeeAttendance;
use Dotenv\Validator;
use Exception;

class ExpeController extends Controller
{
    
    public function index(Request $request){
 
        $att=EmployeeAttendance::get();
        foreach($att as $key => $Data){
            
            $ms_sql=new MsSql;    
            $ms_sql->ID=$Data->finger_print_id;
            $ms_sql->type=$Data->type;
            $ms_sql->datetime=$Data->in_out_time;
            $ms_sql->status=0;
            $ms_sql->employee=$Data->employee_id;
            $ms_sql->device=$Data->device;
            $ms_sql->device_employee_id=$Data->device_employee_id;
            $ms_sql->save();
            
        }

        dd('hai');

    }


 
}
