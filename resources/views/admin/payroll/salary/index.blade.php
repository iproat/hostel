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
<script>
    jQuery(function() {
        $("#attendanceRecord").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
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

                        <br>

                        <div class="col-md-1"></div>
                            {{ Form::open([
                                'route' => 'salary.index',
                                'id' => 'dailyAttendanceReport',
                                'class' => 'form-horizontal',
                                'method'=>'GET'
                            ]) }}
                            <div class="form-group">

                                <div class="col-md-2">
                                    <div class="form-group">
                                       <label class="control-label" for="email">@lang('salary.employee_name')<span
                                                class="validateRq"> </span></label>
                                        <select class="form-control employee_id select2  "  
                                            name="employee_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if (isset($_REQUEST['employee_id'])) @if ($_REQUEST['employee_id'] == $value->employee_id) {{ 'selected' }} @endif
                                                    @endif>{{ $value->first_name." ".$value->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                               

                                <div class="col-md-2" style="margin-left:24px;">
                                    <div class="form-group">
                                        <label class="control-label" for="department_id">@lang('common.department'):</label>
                                        <select name="department_id" class="form-control department_id  select2">
                                            <option value="">--- @lang('common.please_select') ---</option>
                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if ($value->department_id == $department_id) {{ 'selected' }} @endif>
                                                    {{ $value->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2" style="margin-left:24px;">
                                    <div class="form-group">
                                        <label class="control-label" for="email">Month & Year<span
                                                class="validateRq">*</span>:</label>
                                        <input type="text" class="form-control monthField" style="height: 35px;"
                                            required readonly placeholder="@lang('common.date')" id="date"
                                            name="date"
                                            value="@if (isset($date)) {{ $date }}@else {{ date('Y-m') }} @endif">
                                    </div>
                                </div>
                                <div class="col-sm-0"></div>
                                <div class="col-sm-1">
                                    <label class="control-label col-sm-1 text-white"
                                        for="email">@lang('common.date')</label>
                                    <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
                                        class="btn btn-info " value="@lang('common.filter')">
                                </div>
                            </div>
                            {{ Form::close() }}

                        </div>

                        <br>
                        @if(isset($_GET['employee_id']))
                        <h4 class="text-right">
                             <a class="btn btn-success" style="color: #fff"
                                        href="{{route('salary.reportdownload',['employee'=>$_GET['employee_id'],'department'=>$_GET['department_id'],'date'=>$_GET['date']])}}"><i class="fa fa-download fa-lg" aria-hidden="true"></i> EXCEL DOWNLOAD</a>
                        </h4>
                        @endif
                        <div class="table-responsive" style="overflow-x:scroll; ">
                        
                            <table id="payslip" class="table table-bordered table-responsive" style="font-size: 12px;width:100%;">
                                <thead class="tr_header">
                                    <tr>
                                        <th>Id</th>
                                        <th>Employee Name</th>  
                                        <th>Department</th>
                                        <th>Month&Year</th> 
                                        <th>Salary</th>
                                        <th>Total Working Days</th>                                         
									    <th>Gross Salary</th>
									    <th>Total Deduction</th>
									    <th>Net Salary</th>
                                        <th>Payslip</th>
                                    </tr>
                                </thead>
                                <tbody >							
									
							@php

                            $qry="1 ";
                            if(isset($_GET['employee_id'])){
                            
                            
                            if($_GET['employee_id'])
                                $qry.=" AND employee=".$_GET['employee_id'];

                           

                            if($_GET['department_id'])
                                $qry.=" AND department=".$_GET['department_id'];

                            if($_GET['date']){
                                $expl=explode("-",$_GET['date']);
                                $qry.="  AND ( month=".(int)$expl[1]." AND year=".(int)$expl[0].")";
                            }
                                
                            }
                            
							$payroll=App\Model\Payroll::whereRaw("(".$qry.")")->orderBy('payroll_id', 'DESC')->get();
                            
								if($payroll){
                                    $s = 1;
									foreach($payroll as $payrollid){
                                        $month=$payrollid->month;
                                        $year = $payrollid->year;
									 $employee = App\Model\Employee::find($payrollid->employee);
                                     if($employee->department_id){
                                        $departmentdata=App\Model\Department::find($employee->department_id);
                                        $department =$departmentdata->department_name;
                                     }else{
                                        $department="";
                                     }
                                     
                                      
                                    @endphp

	
									<tr>
									<td>{{$s++}}</td>	 
									<td>{{$employee->first_name.''.$employee->last_name}}</td>									 
									<td>{{$department}}</td>
									<td>{{$payrollid->month.'-'.$payrollid->year }}</td>                                     
									<td>{{round($employee->salary)}} </td>
                                    <td>{{round($payrollid->total_paying_days)}}</td> 									 
									<td>{{round($payrollid->gross_salary)}}</td>
									<td>{{round($payrollid->esi_amount+$payrollid->employee_pf+$payrollid->advance_deduction)}}</td>
									<td>{{round($payrollid->net_salary)}}</td>	
									<td><a id="link" class="btn btn-success " href="{{URL::to('/payslip/'.$payrollid->payroll_id)}}" target="_blank" style="color:#fff;"> PDF </a></td> 
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