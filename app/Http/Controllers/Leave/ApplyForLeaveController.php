<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyForLeaveRequest;
use App\Model\LeaveApplication;
use App\Model\LeaveType;
use App\Model\LeaveConfigure;
use App\Model\PaidLeaveApplication;
use App\Model\Employee;
use App\Repositories\CommonRepository;
use App\Repositories\LeaveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyForLeaveController extends Controller
{

    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository  = $leaveRepository;
    }

    public function index()
    {
        if(session('logged_session_data.employee_id') != 1){
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy'])
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->orderBy('leave_application_id', 'desc')
            ->paginate(10);
        }elseif(session('logged_session_data.employee_id') == 1){
            $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy'])
            ->orderBy('leave_application_id', 'desc')
            ->paginate(10);
        }
        return view('admin.leave.applyForLeave.index', ['results' => $results]);    
    }

    public function create()
    {
        $leaveTypeList   = $this->commonRepository->leaveTypeList();
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        $employeeList=$this->commonRepository->employeeList();
        return view('admin.leave.applyForLeave.leave_application_form', ['leaveTypeList' => $leaveTypeList,'getEmployeeInfo' => $getEmployeeInfo,'employeeList'=>$employeeList]);
    }

    public function getEmployeeLeaveBalance(Request $request)
    {
        $leave_type_id = $request->leave_type_id;
        $employee_id   = $request->employee_id;
        if ($leave_type_id != '' && $employee_id != '') {

            $employee=Employee::find($request->employee_id);

            $curr_month=DATE('m');
            $leave_config=LeaveConfigure::where('designation',$employee->designation_id)->first();
            if($leave_type_id==3){ //CL
        
                $taken_cl=LeaveApplication::where('leave_type_id',3)->where('employee_id',$employee->employee_id )->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                $balance=$leave_config->cl_days-$taken_cl;
            
                // if($employee->designation_id==2){

                //     $curent_month=DATE('n');

                //     // $taken_cl=LeaveApplication::where('leave_type_id',1)->where('employee_id',$employee->employee_id )->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                //     $taken_cl=LeaveApplication::where('leave_type_id',1)->where('employee_id',$employee->employee_id )->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                //     $balance=$leave_config->cl_days-$taken_cl;
            

                // }
            }elseif($leave_type_id==4){ //SL

                $taken_el=LeaveApplication::where('leave_type_id',4)->where('employee_id', $employee->employee_id)->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                $balance=$leave_config->sl_days-$taken_el;

                if($employee->designation_id==2){ $balance=0; }

            }elseif($leave_type_id==1){ //AL

                $taken_el=LeaveApplication::where('leave_type_id',1)->where('employee_id', $employee->employee_id)->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                $balance=$leave_config->al_days-$taken_el;

                // if($employee->designation_id==2){ $balance=0; }

            }elseif($leave_type_id==2){ //ML
                
                $taken_el=LeaveApplication::where('leave_type_id',2)->where('employee_id', $employee->employee_id)->where('status','=',2)->whereYear('created_at', Carbon::now()->year)->sum('number_of_day');
                $balance=$leave_config->ml_days-$taken_el;


            }
            return $balance;
            //return $this->leaveRepository->calculateEmployeeLeaveBalance($leave_type_id, $employee_id);
        }
    }

   

    public function applyForTotalNumberOfDays(Request $request)
    {
        $application_from_date = dateConvertFormtoDB($request->application_from_date);
        $application_to_date   = dateConvertFormtoDB($request->application_to_date);
        return $this->leaveRepository->calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date);
    }

    public function store(ApplyForLeaveRequest $request)
    {
        
        $employee_data = Employee::where('employee_id', $request->employee_id)->first();
        $input                          = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date']   = dateConvertFormtoDB($request->application_to_date);
        $input['application_date']      = date('Y-m-d');
        $input['branch_id']             = $employee_data->branch_id;
        $hod=Employee::where('user_id',$employee_data->supervisor_id)->first();  
        if($hod->employee_id){
            $head = $hod->employee_id;
        } else{
            $head = 1;
        }    
         
                  
        try { 
            $applicationexist=LeaveApplication::where('application_from_date','>=',dateConvertFormtoDB($request->application_from_date))->where('application_to_date','<=',dateConvertFormtoDB($request->application_to_date))->where('employee_id', $request->employee_id)->first();
			 
            if(!$applicationexist){             	
                LeaveApplication::create($input);
                $bug = 0;
            }else{                
                return redirect('applyForLeave')->with('error', 'Request Already Exist !.');
            }

	   try{            
 		//Email notification
             	$emp=Employee::find($request->employee_id);
             	$hod=Employee::where('user_id',$emp->supervisor_id)->first();

             	if($hod->email){                    
                 	$maildata = \App\Components\Common::mail('mail/headleavenotification',$hod->email,'Leave Request Notification',['head_name'=>$hod->first_name.' '.$hod->last_namr,'request_info'=> $emp->first_name.' '.$emp->last_namr.'have requested for leave (for '.$request->purpose.') from '.' '.dateConvertFormtoDB($request->application_from_date).' to '. dateConvertFormtoDB($request->application_to_date),'status_info'=>'']);
 		}

            }catch (\Exception $ex) { 
  		//dd($ex);
	    }
             //End Email Notification
            
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return redirect('applyForLeave')->with('success', 'Leave application successfully send.');
        }elseif($bug == 2){
            return redirect('applyForLeave')->with('error', 'Request Already Exist !.');
        } else {
            return redirect('applyForLeave')->with('error', 'Something error found !, Please try again.');
        }
    }

}
