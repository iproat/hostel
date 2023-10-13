@php
use App\Components\Common; 
@endphp
@extends('admin.master')
@section('content')
@section('title')
    @lang('salary.salary_details')
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
                        

                        <br>
                        <div class="table-responsive" style="overflow-x:scroll; ">
                            <table id="payslip" class="table table-bordered table-responsive" style="font-size: 12px;width:100%;">
                                <thead class="tr_header">
                                    <tr>
                                        <th>Employee Id</th>
                                        <th>Employee Name</th>  
                                        <th>Department</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Salary</th>
                                        <th>Per Day Salary</th> 
										<th> Worked Salary  </th>
										<th>Basic (%)</th>
										<th> Basic Salary </th>
										<th>HRA% </th>
										<th>Overtime Hours</th>
										<th>OT Amount</th>
										<th>ESI</th>
									    <th>PF</th>
									    <th>Gross Salary</th>
									    <th>Total Desuction</th>
									    <th>Net Salary</th>
                                        <th>Payslip</th>
                                    </tr>
                                </thead>
                                <tbody >							
									
									@php
									$payroll=App\Model\Payroll::orderBy('payroll_id','DESC')->get();
									if($payroll){
									 foreach($payroll as $payrollid){
                                        $month=$payrollid->month;
                                        $year = $payrollid->year;
									 
									$employee = App\Model\Employee::where('employee_id',$payrollid->employee)->first(); 
									$department = App\Model\Department::where('department_id',$payrollid->department)->first();	
                                    $emplyeetotalot = App\Model\EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('status','=',1)->where('over_time','!=',NULL)->get();
        
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
									
								 
									$workeddays = App\Model\EmployeeInOutData::where('finger_print_id', '=', $employee->finger_id)->whereMonth('date', '=', $month)->whereYear('date', '=', $year)->whereIN('attendance_status',[1,4])->where('status','=',1)->count();
       	
										
									 
									 
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
									$totalesipf = $PF + $ESI; 
																		 
									$OTAmount = round($overtime * $PerHourSalary);	

									@endphp

	
									<tr >
									<td>{{$employee->employee_id}}</td>	 
									<td>{{$employee->first_name.''.$employee->last_name}}</td>									 
									<td>{{$department->department_name}}</td>
									<td>{{$payrollid->month}}</td>
									<td>{{$payrollid->year}}</td> 
                                    <td>{{$payrollid->per_day_da}}</td>
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
									<td>{{$totalesipf}}</td>
									<td>{{$GrossSalary - $totalesipf}}</td>	
									<td><a id="link" href="{{URL::to('/payslip/'.$payrollid->payroll_id)}}"> Download this file  </a></td>
									</tr>
									@php
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
<script type="text/javascript">
    $(function() {
        $('#payslip').DataTable({
            
        });
    });
    
</script>
@endsection('page_scripts')