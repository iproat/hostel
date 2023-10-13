<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyForPermissionRequest;
use App\Model\LeavePermission;
use App\Model\LeaveType;
use App\Model\LeaveConfigure;
use App\Model\PaidLeaveApplication;
use App\Model\Employee;
use App\Repositories\CommonRepository;
use App\Repositories\LeaveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyForPermissionController extends Controller
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
        $results = LeavePermission::with(['employee'])
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->orderBy('leave_permission_date', 'desc')
            ->paginate(10);
        }elseif(session('logged_session_data.employee_id') == 1){
            $results = LeavePermission::with(['employee'])
            // ->where('employee_id', session('logged_session_data.employee_id'))
            ->orderBy('leave_permission_date', 'desc')
            ->paginate(10);
        }         
        return view('admin.leave.applyForPermission.index', ['results' => $results]);
    }

    public function create()
    {        
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        $employeeList=$this->commonRepository->employeeList();
        return view('admin.leave.applyForPermission.leave_permission_form', ['getEmployeeInfo' => $getEmployeeInfo,'employeeList'=>$employeeList]);
    }  

   

    public function applyForTotalNumberOfPermissions(Request $request)
    {
        $permission_date = dateConvertFormtoDB($request->permission_date); 
        $employee_id = $request->employee_id; 
        $Year  = date("Y",strtotime($permission_date));
        $Month = (int)date("m",strtotime($permission_date));
        $checkpermissions = LeavePermission::whereMonth('leave_permission_date','=',$Month)->whereYear('leave_permission_date','=',$Year)
        ->where('employee_id',$employee_id)->where('status',2)->count();

        return $checkpermissions;
         
    }

    public function store(ApplyForPermissionRequest $request)
    {
        $employee_data = Employee::where('employee_id', $request->employee_id)->first();


        $input                            = $request->all();
        $input['leave_permission_date']   = dateConvertFormtoDB($request->permission_date);
        $input['permission_duration']     = $request->permission_duration;
        $input['leave_permission_purpose']= $request->purpose;        
        $input['from_time']               = $request->from_time;
        $input['to_time']                 = $request->to_time;
        
        
        $year = date('Y',strtotime($request->permission_date));
        $month = date('m',strtotime($request->permission_date)); 

        
            // if(date('Y-m-d',strtotime($request->permission_date)) < strtotime(date('Y-m-d'))) {
            // // return response()->json([
            // //     'message' => 'Leave cannot be applied for completed days.', 
            // //     'status' => false,
            // // ], 201);
            // return redirect('applyForPermission')->with('error', 'Leave cannot be applied for completed days.');            
            // }

        $if_exists = LeavePermission::where('employee_id',$request->employee_id)->where('leave_permission_date',dateConvertFormtoDB($request->permission_date))->first();
        
        if ($if_exists) {
            return redirect('applyForPermission')->with('error', 'Request Already Exist');          
            
        } elseif (!$if_exists) {

            try {             
                LeavePermission::create($input);                
                $bug = 0;

        //        try {   
     	// 	//Email notification
        //             $emp=Employee::find($request->employee_id);
        //             $hod=Employee::where('user_id',$emp->supervisor_id)->first();


        //             if($hod->email){                    
        //                 \App\Components\Common::mail('mail/headpermissionnotification',$hod->email,'Permission Request Notification',['head_name'=>$hod->first_name.' '.$hod->last_namr,'request_info'=> $emp->first_name.' '.$emp->last_namr.'have requested for permission (for '.$request->purpose.') on '.' '.DATE('d-m-Y',strtotime(dateConvertFormtoDB($request->permission_date))),'status_info'=>'']);
        //             }
                    
        //             //End Email Notification
        //          } catch (\Exception $ex) {
		//  }

            } catch (\Exception $e) {
                $bug = $e->errorInfo[1];
            }

    
            if ($bug == 0) {            
                return redirect('applyForPermission')->with('success', 'Permission Request successfully send.');
            } else {
                return redirect('applyForPermission')->with('error', 'Something error found !, Please try again.');
            }

        } 
    }
    public function permissionrequest()
    {
        // $departmentresults = LeavePermission::where('department_head',Auth::user()->user_id)->where('department_approval_status',0)->where('status',1)->paginate(10);
        // $plantresults = LeavePermission::where('plant_head',Auth::user()->user_id)->where('plant_approval_status',0)->where('status',1)->paginate(10);
        $permissionresults = LeavePermission::where('status',1)->paginate(10);
        // $plantresults = LeavePermission::where('plant_head',Auth::user()->user_id)->where('plant_approval_status',0)->where('department_approval_status',1)->where('status',1)->paginate(10);
        
        
            return view('admin.leave.applyForPermission.permission_requests', ['permissionresults' => $permissionresults]);
        
    }
    

}
