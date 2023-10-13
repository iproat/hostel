@php
use App\Components\Common; 
@endphp
@extends('admin.master')
@section('content')
@section('title')
    @lang('common.view_salary_data')
@endsection
<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }

    /*
  tbody {
   display:block;
   height:500px;
   overflow:auto;
  }
  thead, tbody tr {
   display:table;
   width:100%;
   table-layout:fixed;
  }
  thead {
   width: calc( 100% - 1em )
  }*/
</style>
 
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.Payslip')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <hr>
	 
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">  
                        <div class="table-responsive">
						{{ Form::open(['route' => 'salary.multiplepayslip', 'id' => 'mEmpPaySlip', 'method' => 'POST']) }}
						<div class="row">
        					<div class="col-sm-12">    
                                <div class="form-group"> 
                                    <input type = "hidden" class="form-control paySlipMonth required" required  name="month" value="{{$month}}"> 
									<input type = "hidden" class="form-control paySlipYear required" required  name="year" value="{{$year}}">  
                                    <input type="submit" id="generate_msalary" style="width: 150px;float:right;"
                                            class="btn btn-info " value="@lang('common.generate_salary')">                                            
                                </div>                                
                        {{ Form::close() }}
							</div>
						</div>
                            <table id="payslip" class="table table-bordered table-responsive" style="font-size: 12px; overflow-x: auto;">
                                <thead class="tr_header">
                                    <tr>
                                        <th>Employee Id</th>
                                        <th>Employee Name</th>  
                                        <th>Department</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Salary</th>
                                        <th>Per Day Salary</th> 
										<th>Worked Salary  </th>
										<th>Basic (%)</th>
										<th> Basic Salary </th>
										<th>HRA% </th>
										<th>Overtime Hours</th>
										<th>OT Amount</th>
										<th>ESI</th>
									    <th>PF</th>
									    <th>Gross Salary</th>
									    <th>Total Deduction</th>
									    <th>Net Salary</th>
										<th>Advance Deduction</th>
										<th>ESI</th>
                                    </tr>
                                </thead>
                                <tbody>
								
									
									@php
									 
							if($employees){
							$employeeot='';
							foreach($employees as $emp_id){
								$employeeattendancedata = App\Model\EmployeeInOutData::where('finger_print_id', '=', $emp_id->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->first();

								if(!empty($employeeattendancedata)){
									
									$checkdup =App\Model\Payroll::where('employee',$emp_id->employee_id)->where('month',$month)->where('year',$year)->first(); 

									if(empty($checkdup)){

									$emplyeetotalot = App\Model\EmployeeInOutData::where('finger_print_id', '=', $emp_id->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
        
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
									$overtime = $hours;
									
								 
									$workeddays = App\Model\EmployeeInOutData::where('finger_print_id', '=', 
        $emp_id->finger_id)->whereMonth('date', '=',
         $month)->whereYear('date', '=', $year)->where('working_time', '!=','00:00:00')->where('status','=',1)->count();
       	
									
									$employee = App\Model\Employee::where('employee_id',$emp_id->employee_id)->first(); 
									
									$department = App\Model\Department::where('department_id',$employee->department_id)->first();
									$workingDays = Common::workingDays();
									$ESalary = round($employee->salary);									
									$settings=App\Model\PayrollSettings::where('payset_id',1)->first();	
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
																		 
									$OTAmount = round($overtime * $PerHourSalary);	
									$salary_date = $year.'-'.$month.'-01';
									
									$advdeductionamount = 0;
									$advancededuction=App\Model\AdvanceDeduction::where('employee_id', '=', $emp_id->employee_id)->where('status','=','1')->first();
        							if(!empty($advancededuction)){
									$amount = $advancededuction->deduction_amouth_per_month;
									$date = $advancededuction->date_of_advance_given;
									$start_date = new DateTime($date);
									$total_period = $advancededuction->no_of_month_to_be_deducted + 1;
									$end_period = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->addMonth($total_period);
										if(date("d-m-Y", strtotime($end_period)) > (date("d-m-Y",strtotime($salary_date)))){
											$advdeductionamount = $advancededuction->deduction_amouth_per_month;

										}else{
											$advdeductionamount = 0;
										}
									}
									@endphp	
									<tr>
									<td>{{$employee->employee_id}}</td>	 
									<td>{{$employee->first_name.''.$employee->last_name}}</td>									 
									<td>{{$department->department_name}}</td>
									<td>{{$month}}</td>
									<td>{{$year}}</td>
									<td>{{$emp_id->salary}}</td>
									<td>{{$PerDaySalary}}</td> 
									<td>{{$WorkedSalary}} </td>
									<td>{{round($settings->basic)}}% </td>
									<td>{{$BasicSalary}} </td>
									<td>{{round($settings->hra)}}% </td>
									<td>{{$overtime}} </td>
									<td>{{$OTAmount }} </td>
									<td>{{$ESI}}</td>
									<td>{{$PF}}</td>
									<td>{{$GrossSalary}}</td>
									<td>{{ $totalesipf  }}</td>
									<td>{{$GrossSalary - $totalesipf}}</td>	
									<td>{{$advdeductionamount}}</td>	
									<td>{{$ESI}}</td>	
																 
									</tr>
									@php
								}
									 }
								}
							}
									@endphp                                   
                                </tbody>
                            </table>
							
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts') 
@endsection('page_scripts')