@php
use App\Components\Common;
@endphp
@extends('admin.master')
@section('content')
@section('title')
	@lang('salary.salary_sheet') {{ $employee->first_name." ".$employee->last_name }} ( {{$employee->finger_id}} )
@endsection
@php
	$salary_date = $request->year.$request->month.'-01';
	$advdeductionamount = 0;
	$advancededuction=App\Model\AdvanceDeduction::where('employee_id', '=', $employee->employee_id)->where('status','=','1')->first();
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
		
	$workingDays = Common::workingDays();
	$ESalary = round($employee->salary);									
	$settings=App\Model\PayrollSettings::where('payset_id',1)->first();	
	$PerDaySalary = round($ESalary / $workingDays);	
	$PerHourSalary = round($PerDaySalary / $settings->working_hours);
	$WorkedSalary =0;
	if($workeddays == 0){
		$WorkedSalary =0;
	}else{
		$WorkedSalary =round($ESalary / $workingDays * $workeddays);
	}																	 

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

	$totaldeductionamount = $totalesipf + $advdeductionamount;


	@endphp	

<style type="text/css">
	td{padding: 4px !important;}
</style>
<script>
	jQuery(function() {
		$("#monthlyDeduction").validate();
		$("#month").change(function(){
            $('.paySlipMonth').val(this.value);
        });     
	});
</script>
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
						@lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						@if ($errors->any())
						<div class="alert alert-danger alert-block alert-dismissable">
							<ul>
								<button type="button" class="close" data-dismiss="alert">x</button>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
						@if (session()->has('success'))
							<div class="alert alert-success alert-dismissable">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<i
									class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
							</div>
						@endif
						@if (session()->has('error'))
							<div class="alert alert-danger alert-dismissable">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<i
									class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
							</div>
						@endif
						
						<div class="row"> 
							<div class="col-md-12">
								<table class="table table-bordered table-hover table-striped">
									<tbody>
										<tr>
										 	<td>Name</td><td>{{$employee->first_name." ".$employee->last_name}}</td>
										 	<td>Date of Joining</td>
											<td>{{ ($employee->date_of_joining && $employee->date_of_joining !="0000-00-00") ?? DATE('d-m-Y',strtotime($employee->date_of_joining)) }}</td>
										</tr>
										<tr>
											<td>Designation</td><td>{{ $employee->designation->designation_name }}</td>
											<td>Month</td><td>{{ DATE('M',strtotime($request->month)) }}</td>
										</tr>
										<tr>
											<td>Department</td><td>{{$employee->department->department_name }}</td>
											<td>Year</td><td>{{ DATE('Y',strtotime($request->month)) }}</td>
										</tr>
										<tr>
											<td>Date of Birth</td><td>{{ ($employee->date_of_birth && $employee->date_of_birth !="0000-00-00") ?? DATE('d-m-Y',strtotime($employee->date_of_birth)) }}</td>
											<td>No of Working Days</td><td>{{$workeddays}}</td>											
										</tr>
										 <tr>
										 <td></td><td></td>
										 <td>Absent Days</td><td>{{$workingDays - $workeddays}}<input type = "hidden" class="form-control  required" required  name="worked_days" value="{{$workeddays}}"></td></tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<h3>Wages Earning</h3>
								{{ Form::open(['route' => 'salary.singlepayslip', 'id' => 'sEmpPaySlip', 'method' => 'POST']) }}
								<table class="table table-bordered table-hover table-striped">
									<tbody>
									
										<tr><td>Basic Salay</td><td> {{$employee->salary}} <input type = "hidden" class="form-control  required" required  name="empsalary" value="{{$employee->salary}}" ></td></tr>
										<tr><td>Per Day Salary</td><td>{{$PerDaySalary}} <input type = "hidden" class="form-control  required" required  name="perdaysalary" value="{{$PerDaySalary}}" ></td></tr>
										<tr><td>Per Hours Salary</td><td>{{$PerHourSalary}} <input type = "hidden" class="form-control  required" required  name="perhoursalary" value="{{$PerHourSalary}}"></td></tr>
										<tr><td>Worked Salary</td><td>{{$WorkedSalary}} <input type = "hidden" class="form-control  required" required  name="workedsalary" value="{{$WorkedSalary}}"></td></tr>										 
										<tr><td>Basic ({{round($settings->basic)}}%) <input type = "hidden" class="form-control  required" required  name="basic_pay" value="{{round($settings->basic)}}"></td>
										<td>{{$BasicSalary}}<input type = "hidden" class="form-control  required" required  name="basic_salary" value="{{$BasicSalary}}"></td></tr>
										<tr><td>HRA ({{round($settings->hra)}}%)</td><td>{{$HRA }}<input type = "hidden" class="form-control  required" required  name="hra" value="{{round($settings->hra)}}"></td></tr>
										<tr><td>Over Time</td><td>{{$overtime}}<input type = "hidden" class="form-control  required" required  name="overtime" value="{{$overtime}}"></td></tr>
										<tr><td>OT Amount</td><td>{{$OTAmount }}<input type = "hidden" class="form-control  required" required  name="ot_amount" value="{{$OTAmount }}"></td></tr>
									</tbody>
									
								</table>
							</div>
							 <div class="col-md-6">
								<h3>Deduction</h3>
								<table class="table table-bordered table-hover table-striped">
									<tbody>
										<tr><td>Employee ESI</td><td> {{$ESI}}<input type = "hidden" class="form-control  required" required  name="esi" value="{{$ESI}}"</td></tr>
										<tr><td>Employee PF</td><td>{{$PF}}<input type = "hidden" class="form-control  required" required  name="pf" value="{{$PF}}"></td></tr>
										<tr><td>Advance Deduction</td><td>{{$advdeductionamount}}<input type = "hidden" class="form-control  required" required  name="adv_amount" value="{{$advdeductionamount}}"></td></tr>
									</tbody>
									
								</table>
							</div> 
						</div>
						 <div class="row"> 
							<div class="col-md-6 col-md-offset-3">
								<table class="table table-bordered table-hover table-striped">
									<tr><td><b>Wages Eaerning</b></td><td>{{$GrossSalary}}<input type = "hidden" class="form-control  required" required  name="gross_Salary" value="{{$GrossSalary}}"></td></tr>
									<tr><td><b>Deduction</b></td><td>{{ $totaldeductionamount  }}<input type = "hidden" class="form-control  required" required  name="totalesipf" value="{{ $totaldeductionamount  }}"></td></tr>
									<tr><td><b>Net Amount</b></td><td>{{$GrossSalary - $totaldeductionamount}}<input type = "hidden" class="form-control  required" required  name="net_salary" value="{{$GrossSalary - $totaldeductionamount}}"></td></tr>
								</table>
							</div>
						</div> 
					   	 <br>
							
                                <div class="col-md-6 text-center">
                                    <div class="form-group"> 
                                    <input type = "hidden" class="form-control required" required  name="payslipmonth" value="{{$request->month}}">
									<input type = "hidden" class="form-control required" required  name="employee_id" value="{{$request->employee_id}}" >
                                        <input type="submit" id="generate_ssalary" style="margin-top: 25px; width: 150px;"
                                            class="btn btn-info " value="@lang('common.generate_salary')">                                            
                                    </div>
                                </div>
                                {{ Form::close() }}
					 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
