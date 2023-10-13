<?php

namespace App\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Components\Common;
use App\Model\PayrollSettings;
use App\Model\EmployeeInOutData;
use App\Model\Payroll;
use App\Model\AdvanceDeduction;
use App\Model\Department;
use App\Model\Branch;
use App\Model\MsSql;
use App\Model\OverTime;
use App\Model\Designation;
use App\Exports\SalaryReport;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use DateTime;

class SalaryController extends Controller{

    public function index(Request $request){
        \set_time_limit(0);
       
        $departmentList = Department::get();
        $branchList = Branch::get();
        $date = $request->date;
        $branch_id= $request->branch_id;
        $department_id =$request->department_id;
        $attendance_status=$request->attendance_status;
        $employeeList = Employee::get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->branch_id, $request->attendance_status);
        }
        return \view('admin.payroll.salary.index',compact('branchList','departmentList','date','branch_id','department_id','attendance_status','employeeList'));
    }


    public function details(Request $request){
        $time=strtotime($request->date.'-01' );
        $month=date("m",$time);
        $year=date("Y",$time);

        $qry="1 ";
        if($request->employee)
            $qry.=" AND employee=".$request->employee;

        if($request->branch)
            $qry.=" AND branch=".$request->branch;

        if($request->department)
            $qry.=" AND department=".$request->department;

        // if($request->date)
        //     $qry.=" AND 'month'=".$month; 
        //     $qry.=" AND 'year'=".$year;  

 
        $data = Payroll::where('status', '!=', 2)->whereRaw("(".$qry.")")->orderBy('created_at', 'DESC');
        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                return
                    '<a href="' . route('genration.index', ['id' => $data->payroll_id]) . '" class="btn btn-xs btn-primary" title="Payslip" target="_blank" data-id="' . $data->payroll_id . '"><i class="fa fa-file-pdf-o"></i></a>';
            })
        
            ->editColumn('employee', function ($data) {
                $emp = Employee::find($data->employee);
                if ($emp) {
                    return $emp->first_name." ".$emp->last_name;
                }
            })
            ->editColumn('month', function ($data) {
               $month="01-".$data->month."-".DATE('Y');
               return DATE('M',strtotime($month));
            })

            /*->addColumn('status', function ($data) {
                return $data->status == 1 ? '<div style="text-align:center;"><i class="fa fa-check" style="color:green;" aria-hidden="true"></div>'   : '<div style="text-align:center;"><i class="fa fa-times" style="color:red;" aria-hidden="true"></div>';
            })
            ->editColumn('department', function ($data) {
                $department = Department::where('department_id', $data->department)->first();
                if ($department) {
                    return $department->name;
                }
            })
            ->editColumn('current_year', function ($data) {
                $current_year = Year::where('year_id', $data->current_year)->first();
                if ($current_year) {
                    return $current_year->name;
                }
            })
            ->editColumn('photo', function ($data) {
                if ($data->photo)
                    return '<img src="../uploads/studentphoto/' . $data->photo . '" style="width:50px;">';
            })
            ->editColumn('updated_at', function ($data) {
                return DATE('d-m-Y h:i A', strtotime($data->updated_at));
            })
            ->editColumn('fees_status', function ($data) {
                return Common::feestatus($data->fees_status);
            })*/
            ->rawColumns(['action'])
            ->make(true);
    }

    public function list($value='')
    {
        // code...
    }
    
    public function generation(Request $request){
        $employeeList = Employee::get();
        return view('admin.payroll.salary.generation', ['employeeList' => $employeeList]);
    }

    public function sheet(Request $request){
        $time=strtotime($request->month.'-01');
        $month=date("m",$time);
        $year=date("Y",$time);
        $employee = Employee::find($request->employee_id);
        $emplyeetotalot = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
        
        $employeeot = [];        
        foreach($emplyeetotalot as $othour){
            $employeeot[]= $othour->over_time;  
        }
        $emplyeetotalot = $this->totalhours($employeeot);
        
        //dd($emplyeetotalot);
        $emplyworkeddays = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('working_time', '!=','00:00:00')->where('status','=',1)->count();         
        return view('admin.payroll.salary.sheet', ['employee'=>$employee,'request'=>$request,'overtime'=>$emplyeetotalot,'workeddays'=>$emplyworkeddays]);
    }

    public function totalhours($times) {
        $minutes = 0; //declare minutes either it gives Notice: Undefined variable
        // loop throught all the times
        foreach($times as $key => $time){
            $expl = explode(':',$time);
            $minutes += $expl[0] * 60;
            $minutes += $expl[1];
        }
    
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;
        if($minutes > 30 ){
            $hours = $hours + 1;
        }

        // returns the time already formatted
        return sprintf('%02d', $hours);
    }


     public function report(Request $request){
        \set_time_limit(0);
        $dataProvider = new EloquentDataProvider(Payroll::query());

        $departmentList = Department::get();
        $branchList = Branch::get();
        $date = $request->date;
        $branch_id= $request->branch_id;
        $department_id =$request->department_id;
        $attendance_status=$request->attendance_status;
        $employeeList = Employee::get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->branch_id, $request->attendance_status);
        }
        return \view('admin.payroll.salary.report',compact('dataProvider','branchList','departmentList','date','branch_id','department_id','attendance_status','employeeList'));
    }


    public function reportdetails(Request $request){


        $qry="1 ";
        if($request->employee)
            $qry.=" AND employee=".$request->employee;

        if($request->branch)
            $qry.=" AND branch=".$request->branch;

        if($request->department)
            $qry.=" AND department=".$request->department;

        if($request->date)
            $qry.=" AND date=".$request->date;


        $data = Payroll::where('status', '!=', 2)->whereRaw("(".$qry.")")->orderBy('created_at', 'DESC');
        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                return
                    '<a href="' . route('genration.index', ['id' => $data->payroll_id]) . '" class="btn btn-xs btn-primary" title="Payslip" target="_blank" data-id="' . $data->payroll_id . '"><i class="fa fa-file-pdf-o"></i></a>';
            })
        
            ->editColumn('employee', function ($data) {
                $emp = Employee::find($data->employee);
                if ($emp) {
                    return $emp->first_name." ".$emp->last_name;
                }
            })
            ->editColumn('month', function ($data) {
               $month="01-".$data->month."-".DATE('Y');
               return DATE('M',strtotime($month));
            })

            /*->addColumn('status', function ($data) {
                return $data->status == 1 ? '<div style="text-align:center;"><i class="fa fa-check" style="color:green;" aria-hidden="true"></div>'   : '<div style="text-align:center;"><i class="fa fa-times" style="color:red;" aria-hidden="true"></div>';
            })
            ->editColumn('department', function ($data) {
                $department = Department::where('department_id', $data->department)->first();
                if ($department) {
                    return $department->name;
                }
            })
            ->editColumn('current_year', function ($data) {
                $current_year = Year::where('year_id', $data->current_year)->first();
                if ($current_year) {
                    return $current_year->name;
                }
            })
            ->editColumn('photo', function ($data) {
                if ($data->photo)
                    return '<img src="../uploads/studentphoto/' . $data->photo . '" style="width:50px;">';
            })
            ->editColumn('updated_at', function ($data) {
                return DATE('d-m-Y h:i A', strtotime($data->updated_at));
            })
            ->editColumn('fees_status', function ($data) {
                return Common::feestatus($data->fees_status);
            })*/
            ->rawColumns(['action'])
            ->make(true);
    }

    public function download(Request $request){
        $dataset=[];


        $qry="1 ";
        if($request->employee)
            $qry.=" AND employee=".$request->employee;

       

        if($request->department)
            $qry.=" AND department=".$request->department;

        if($request->date){
            $expl=explode("-",$request->date);
            $qry.="  AND ( month=".(int)$expl[1]." AND year=".(int)$expl[0].")";            
        }

        $payroll=Payroll::whereRaw("(".$qry.")")->get();

        $inc=1;
        foreach ($payroll as $key => $Data) {
            $doj="";
            if($Data->employeeinfo->date_of_joining !="0000-00-00" && !is_null($Data->employeeinfo->date_of_joining))
                $doj=DATE('d-m-Y',strtotime($Data->employeeinfo->date_of_joining));            
            $designation=Designation::find($Data->employeeinfo->designation_id);
            $dataset[] = [ $inc,
                    $Data->employeeinfo->first_name." ".$Data->employeeinfo->last_name,
                    $doj,
                    $designation->designation_name,
                    $Data->total_days,
                    $Data->total_paying_days,                    
                    $Data->total_days-$Data->total_paying_days,
                    $Data->basic_salary,
                    $Data->per_day_wages,
                    $Data->per_hour_salary,
                    $Data->worked_salary,
                    $Data->basic,
                    $Data->hra_amount,
                    $Data->gross_salary,
                    $Data->esi_amount,
                    $Data->employee_pf,
                    $Data->esi_amount+$Data->employee_pf,
                    $Data->ot_amount,
                    $Data->advance_deduction,
                    $Data->net_salary,
                ];
            
            $inc++;
        }
        
        $filename = 'salary-report-'.DATE('d-m-Y-h-i-A').'.xlsx';
        $date=$request->date."-01";
        $extraData = ['subtitle2' => 'Salary Report', 'subtitle3' => ' Month / Year '. DATE('M-Y', strtotime($date)) . ' '];

        $heading=[   [ 'RESICO INDIA PVT LTD.'], 
                [ $extraData['subtitle2'] ], 
                [ $extraData['subtitle3'] ],
                [   'Sr.No.',
                    'Employee Name',
                    'Date of Joining',
                    'Designation',
                    'Total Days',
                    'Days Worked',
                    'Leave',
                    'Basic Salary',
                    'Per Day Salary',
                    'Per Hour Salary',
                    'Worked Salary',
                    'Basic',
                    'HRA',
                    'Gross Salary',
                    'ESI',
                    'PF',
                    'ESI+PF',
                    'OT Amount',
                    'Advance Amount',
                    'Net Salary'

                ]
            ];
        $extraData['heading']=$heading;
        //dd($dataset);
        return \Excel::download(new SalaryReport($dataset, $extraData), $filename);

    }
    public function multiplepayslip(Request $request){          
        
        $month=$request->month;
        $year=$request->year;
        $salary_date = $request->year.'-'.$request->month.'-01';
        $employees = Employee::where('status','=',1)->get();       
        $dataFormat = '';
        $employeeot='';
        $advdeductionamount = 0;
        $empovertime = 0;
       // $count =0;        
        foreach($employees as $employee){ 
            $employeeattendancedata = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->first();
            // dd( $employeeattendancedata);
            $advdeductionamount = 0;

            $advancededuction=AdvanceDeduction::where('employee_id', $employee->employee_id)->where('status','=','1')->first();
            if(($advancededuction)){
            $amount = $advancededuction->deduction_amouth_per_month;
            $date = $advancededuction->date_of_advance_given;
            $start_date = new DateTime($date);
            $total_period = $advancededuction->no_of_month_to_be_deducted + 1;
            $end_period = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->addMonth($total_period);
            
            if(date("d-m-Y", strtotime($end_period)) > (date("d-m-Y",strtotime($salary_date)))){
                $advdeductionamount = $advancededuction->deduction_amouth_per_month ;

            }else{
                $advdeductionamount = 0;
            }
           
            } 
            // dd($advdeductionamount);
            // exit;
            if(!empty($employeeattendancedata)){

                $emplyeetotalot = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
                $employeeot = [];  
            
            foreach($emplyeetotalot as $othour){
                $employeeot[]= $othour->over_time;  
            }
            //$count = count($employee->employee_id);
            $workeddays = EmployeeInOutData::where('finger_print_id', '=', 
            $employee->finger_id)->whereMonth('date', '=',
             $month)->whereYear('date', '=', $year)->where('working_time', '!=','00:00:00')->where('status','=',1)->count();
            
           
            $emplyeetotalot = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
        
									$employeeot = [];        
									foreach($emplyeetotalot as $othour){
										$employeeot[]= $othour->over_time;  
									}
									$minutes = 0; //declare minutes either it gives Notice: Undefined variable
									// loop throught all the times
									foreach($employeeot as $key => $time){
										$expl = explode(':',$time);
										$minutes += $expl[0] * 60;
										$minutes += $expl[1];
									}
								
									$hours = floor($minutes / 60);
									$minutes -= $hours * 60;
									if($minutes > 30 ){
										$hours = $hours + 1;
									} 
									$empovertime = $hours;


            $workingDays = Common::workingDays();
			$ESalary = round($employee->salary);									
			$settings= PayrollSettings::where('payset_id',1)->first();	
			$PerDaySalary = round($ESalary / $workingDays);	
			$PerHourSalary = round($PerDaySalary / $settings->working_hours);																	 
			$WorkedSalary =round($ESalary / $workingDays * $workeddays);
			$BasicSalary = 	round($WorkedSalary *($settings->basic/100));
			$HRA = round($WorkedSalary *($settings->hra/100));
			$GrossSalary = $BasicSalary + $HRA;
			$PF = round($BasicSalary * ($settings->employee_pf / 100));	
			if($ESalary < 21000) {
				$ESI = round($GrossSalary * ($settings->employee_esic / 100));										
			}else{
				$ESI = 0;
			}
            if($employee->generate_salary==2){
                $PF=0;
                $ESI=0;
            }	
			$totalesipf = $PF + $ESI; 
																		 
			$OTAmount = round($empovertime * $PerHourSalary);
            $workinghours = $workeddays/$settings->working_hours;           
            $total_deduction = $totalesipf + $advdeductionamount;
            $netsalary = $GrossSalary - $total_deduction;

            $checkdup =Payroll::where('employee',$employee->employee_id)->where('month',$month)->where('year',$year)->first(); 
            $dataFormat = [];
            $salary = 0;
            if(empty($checkdup)) {
                for ($i=0; $i < 1; $i++) {   
                    if(isset($employee->salary)) {
                        $salary=$employee->salary;
                    }else{
                        $salary = 0; 
                    }               

                $dataFormat[$i] =[
                'employee'                   => $employee->employee_id,
                'finger_print_id'            => $employee->finger_id,
                'month'                      => $month,
                'year'                       => $year,
                'unit'                       => 1,
                'service_provider'           => 12,
                'department'                 => $employee->department_id,
                'no_day_wages'               => 0,
                'total_days'                 => $workingDays,
                'total_paying_days'          => $workeddays,
                'per_day_da'                 => 0,
                'per_day_hra'                => 0,
                'per_day_wages'              => 0,
                'da_amount'                  => 0,
                'hra_amount'                 => $HRA,
                'wages_amount'               => 0,
                'attendance_bonus'           => 0,
                'ot_hours'                   => $empovertime,
                'ot_per_hours'               => 0,
                'ot_amount'                  => $OTAmount,
                'gross_salary'               => $GrossSalary,
                'employee_pf'                => $PF,
                'canteen'                    => 0,
                'net_salary'                 => $netsalary,
                'net_amount'                 => $netsalary,
                'employer_pf'                => 0,
                'service_charge_percentage'  => 0,
                'service_charge'             => 0,
                'bonus_percentage'           => 0,
                'bonus_amount'               => 0,
                'earned_leave'               => 0,
                'leave_amount'               => 0,
                'working_hours'              => $workinghours,
                'working_hours_amount'       => $WorkedSalary,
                'net_amount'                 => $netsalary,
                'da_percentage'              => 0,
                'hra_percentage'             => $settings->hra,
                'employee_pf_percentage'     => $settings->employee_pf, 
                'el_bonus'                   => 0,
                'branch'                     => $employee->branch_id, 
                'advance_deduction'          => $advdeductionamount, 
                'esi_amount'                 => $ESI,                 
                'basic_salary'               => $salary,
                'per_hour_salary'            => $PerHourSalary,
                'per_day_wages'              => $PerDaySalary,
                'worked_salary'              => $WorkedSalary, 
                'basic'                      => $BasicSalary, 

                ];
            }
            if(count($dataFormat) > 0) {
                Payroll::insert($dataFormat);
            }
        // }elseif(isset($checkdup) && !empty($checkdup)){

        //     $data['employee']                   = $employee->employee_id;
        //     $data['finger_print_id']            = $employee->finger_id;
        //     $data['month']                      = $month;
        //     $data['year']                       = $year;
        //     $data['unit' ]                      = 1;
        //     $data['service_provider']           = 12;
        //     $data['department']                 = $employee->department_id;
        //     $data['no_day_wages']               = 0;
        //     $data['total_days']                 = $workingDays;
        //     $data['total_paying_days']          = $workeddays;
        //     $data['per_day_da']                 = 0;
        //     $data['per_day_hra']                = 0;
        //     $data['per_day_wages']              = 0;
        //     $data['da_amount']                  = 0;
        //     $data['hra_amount']                 = $HRA;
        //     $data['wages_amount']               = 0;
        //     $data['attendance_bonus']           = 0;
        //     $data['ot_hours']                   = $empovertime;
        //     $data['ot_per_hours']               = 0;
        //     $data['ot_amount']                  = $OTAmount;
        //     $data['gross_salary']               = $GrossSalary;
        //     $data['employee_pf']                = $PF;
        //     $data['canteen']                    = 0;
        //     $data['net_salary']                 = $netsalary;
        //     $data['employer_pf']                = 0;
        //     $data['service_charge']             = 0;
        //     $data['bonus_percentage']           = 0;
        //     $data['bonus_amount']               = 0;
        //     $data['earned_leave']               = 0;
        //     $data['leave_amount']               = 0;
        //     $data['working_hours']              = $workinghours;
        //     $data['working_hours_amount']       = $WorkedSalary;
        //     $data['net_amount']                 = $netsalary;
        //     $data['da_percentage']              = 0;
        //     $data['hra_percentage']             = $settings->hra;
        //     $data['employee_pf_percentage']     = $settings->employee_pf; 
        //     $data['el_bonus']                   = 0;
        //     $data['branch']                     = $employee->branch_id;
        //     $data['advance_deduction']          = $advdeductionamount;
        //     $data['esi_amount']                 = $ESI;
        //     //dd($checkdup);

        //     Payroll::where('payroll_id ', $checkdup->payroll_id)->update($data); 

        }
        
    }
}
    $dataFormat='';                   
    return redirect('Salary')->with('success', 'Salary Generated Successfully .'); 

    }

    public function singlepayslip(Request $request){          
        $time=strtotime($request->payslipmonth.'-01'); 
        $month=date("m",$time);
        $year=date("Y",$time);
        $salary_date = $year.$month.'-01';      
             
        $dataFormat = '';
        $employeeot='';
        $advdeductionamount = 0;
        $empovertime = 0;

        $employee = Employee::where('employee_id',$request->employee_id)->first();

        if($employee){
           // dd($employee );
        $employeeattendancedata = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->first();
        // dd( $employeeattendancedata);

        $advancededuction=AdvanceDeduction::where('employee_id', '=', $employee->employee_id)->where('status','=','1')->first();
        if(!empty($advancededuction)){
        $amount = $advancededuction->deduction_amouth_per_month;
        $date = $advancededuction->date_of_advance_given;
        $start_date = new DateTime($date);
        $total_period = $advancededuction->no_of_month_to_be_deducted + 1;
        $end_period = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->addMonth($total_period);
        if(date("d-m-Y", strtotime($end_period)) > (date("d-m-Y",strtotime($salary_date)))){
            $advdeductionamount = $advancededuction->deduction_amouth_per_month ;

        }else{
            $advdeductionamount = 0;
        }
        //dd($end_period->format('d-m-Y'));
        }  
       
        if(!empty($employeeattendancedata)){

            $emplyeetotalot = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
            $employeeot = [];  
        
        foreach($emplyeetotalot as $othour){
            $employeeot[]= $othour->over_time;  
        }
        //$count = count($employee->employee_id);
        $workeddays = EmployeeInOutData::where('finger_print_id', '=', 
        $employee->finger_id)->whereMonth('date', '=',
         $month)->whereYear('date', '=', $year)->where('working_time', '!=','00:00:00')->where('status','=',1)->count();
        
       
        $emplyeetotalot = EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
    
                                $employeeot = [];        
                                foreach($emplyeetotalot as $othour){
                                    $employeeot[]= $othour->over_time;  
                                }
                                $minutes = 0; //declare minutes either it gives Notice: Undefined variable
                                // loop throught all the times
                                foreach($employeeot as $key => $time){
                                    $expl = explode(':',$time);
                                    $minutes += $expl[0] * 60;
                                    $minutes += $expl[1];
                                }
                            
                                $hours = floor($minutes / 60);
                                $minutes -= $hours * 60;
                                if($minutes > 30 ){
                                    $hours = $hours + 1;
                                } 
                                $empovertime = $hours;


        $workingDays = Common::workingDays();
        $ESalary = round($employee->salary);									
        $settings= PayrollSettings::where('payset_id',1)->first();	
        $PerDaySalary = round($ESalary / $workingDays);	
        $PerHourSalary = round($PerDaySalary / $settings->working_hours);																	 
        $WorkedSalary =round($ESalary / $workingDays * $workeddays);
        $BasicSalary = 	round($WorkedSalary *($settings->basic/100));
        $HRA = round($WorkedSalary *($settings->hra/100));
        $GrossSalary = $BasicSalary + $HRA;
        $PF = round($BasicSalary * ($settings->employee_pf / 100));	
        if($ESalary < 21000) {
            $ESI = round($GrossSalary * ($settings->employee_esic / 100));										
        }else{
            $ESI = 0;
        }	

        if($employee->generate_salary==2){
            $PF=0;
            $ESI=0;
        }
        $totalesipf = $PF + $ESI; 
                                                                     
        $OTAmount = round($empovertime * $PerHourSalary);
        $workinghours = $workeddays/$settings->working_hours;
        $total_deduction = $totalesipf +  $request->adv_amount;
        $netsalary = $GrossSalary - $total_deduction;
        

        $checkdup =Payroll::where('employee',$employee->employee_id)->where('month',$month)->where('year',$year)->first(); 
        $dataFormat = [];
        $salary = 0; 
        if(empty($checkdup)) {
            for ($i=0; $i < 1; $i++) { 
                if(isset($employee->salary)) {
                    $salary=$employee->salary;
                }else{
                    $salary = 0; 
                }               
               

            $dataFormat[$i] =[
            'employee'                   => $employee->employee_id,
            'finger_print_id'            => $employee->finger_id,
            'month'                      => $month,
            'year'                       => $year,
            'unit'                       => 1,
            'service_provider'           => 12,
            'department'                 => $employee->department_id,
            'no_day_wages'               => 0,
            'total_days'                 => $workingDays,
            'total_paying_days'          => $workeddays,
            'per_day_da'                 => 0,
            'per_day_hra'                => 0,
            'per_day_wages'              => 0,
            'da_amount'                  => 0,
            'hra_amount'                 => $HRA,
            'wages_amount'               => 0,
            'attendance_bonus'           => 0,
            'ot_hours'                   => $empovertime,
            'ot_per_hours'               => 0,
            'ot_amount'                  => $OTAmount,
            'gross_salary'               => $GrossSalary,
            'employee_pf'                => $PF,
            'canteen'                    => 0,
            'net_salary'                 => $netsalary,
            // 'net_amount'                 => $netsalary,
            'employer_pf'                => 0,
            'service_charge_percentage'  => 0,
            'service_charge'             => 0,
            'bonus_percentage'           => 0,
            'bonus_amount'               => 0,
            'earned_leave'               => 0,
            'leave_amount'               => 0,
            'working_hours'              => $workinghours,
            'working_hours_amount'       => $WorkedSalary,
            'net_amount'                 => $netsalary,
            'da_percentage'              => 0,
            'hra_percentage'             => $settings->hra,
            'employee_pf_percentage'     => $settings->employee_pf, 
            'el_bonus'                   => 0,
            'branch'                     => $employee->branch_id, 
            'advance_deduction'          => $request->adv_amount, 
            'esi_amount'                 => $ESI,  
            'basic_salary'               => $salary,
            'per_hour_salary'            => $PerHourSalary,
            'per_day_wages'              => $PerDaySalary,
            'worked_salary'              => $WorkedSalary, 
            'basic'                      => $BasicSalary,                
            ];
        }
        if(count($dataFormat) > 0) {
            Payroll::insert($dataFormat);
        }
    }
    if(isset($checkdup) && (!empty($checkdup))){

        $data['employee']                   = $employee->employee_id;
        $data['finger_print_id']            = $employee->finger_id;
        $data['month']                      = $month;
        $data['year']                       = $year;
        $data['unit' ]                      = 1;
        $data['service_provider']           = 12;
        $data['department']                 = $employee->department_id;
        $data['no_day_wages']               = 0;
        $data['total_days']                 = $workingDays;
        $data['total_paying_days']          = $workeddays; 
        $data['per_day_da']                 = 0;
        $data['per_day_hra']                = 0;
        $data['per_day_wages']              = 0;
        $data['da_amount']                  = 0;
        $data['hra_amount']                 = $HRA;
        $data['wages_amount']               = 0;
        $data['attendance_bonus']           = 0;
        $data['ot_hours']                   = $empovertime;
        $data['ot_per_hours']               = 0;
        $data['ot_amount']                  = $OTAmount;
        $data['gross_salary']               = $GrossSalary;
        $data['employee_pf']                = $PF;
        $data['canteen']                    = 0;
        $data['net_salary']                 = $netsalary;
        $data['net_amount']                 = $netsalary; 
        $data['employer_pf']                = 0;
        $data['service_charge']             = 0;
        $data['bonus_percentage']           = 0;
        $data['bonus_amount']               = 0;
        $data['earned_leave']               = 0;
        $data['leave_amount']               = 0;
        $data['working_hours']              = $workinghours;
        $data['working_hours_amount']       = $WorkedSalary;
        $data['net_amount']                 = $netsalary;
        $data['da_percentage']              = 0;
        $data['hra_percentage']             = $settings->hra;
        $data['employee_pf_percentage']     = $settings->employee_pf; 
        $data['el_bonus']                   = 0;
        $data['branch']                     = $employee->branch_id;
        $data['advance_deduction']          = $request->adv_amount;
        $data['esi_amount']                 = $ESI; 
        $data['basic_salary']               = $employee->salary;
        $data['per_hour_salary']            = $PerHourSalary;
        $data['per_day_wages']              = $PerDaySalary;
        $data['worked_salary']              = $WorkedSalary; 
        $data['basic']                      = $BasicSalary;

        Payroll::where('payroll_id', $checkdup->payroll_id)->update($data);
        }

    }
    
}     
        
$dataFormat='';                   
return redirect('Salary')->with('success', 'Salary Generated Successfully .'); 
} 
     
public function employeessalarydata(Request $request){ 
        $time=strtotime($request->salarydate.'-01');
        $month=date("m",$time);
        $year=date("Y",$time);
        $employees = Employee::where('status',1)->get();      
       
         
        return view('admin.payroll.payslip.list', ['employees'=>$employees, 'month'=>$month,'year'=>$year]);
    }


}