@extends('admin.master')
@section('content')
@section('title')
    @lang('salary.generation')
@endsection
<style>
    .departmentName {
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

    .custom-file-upload {
        color: grey !important;
        display: inline-block;
        padding: 4px 4px 4px 4px;
        cursor: pointer;
        font-weight: normal;
        /* border: 2px solid #3f729b; */
        border-radius: 6px;
        width: 500px;
        height: 32px;

    }

    input::file-selector-button {
        display: inline-block;
        font-weight: bolder;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        background: #41b3f9;
        /* background: #3f729b; */
        /* background: #7ace4c; */
        border-width: 1px;
        border: none;
        font-size: 12px;
        overflow: hidden;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background-size: 12px 12px;
        padding: 4px 4px 4px 4px;
    }
</style>
{{-- @php
if (isset($results)) {
    dd($result);
}
@endphp --}}
<script>
    jQuery(function() {
        $("#monthlyDeduction").validate(); 
        $("#month").change(function(){
            $('.paySlipMonth').val(this.value);
        });  
        $("#mEmpPaySlip").validate();     
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
                            <h2>Generate to Individual Employee</h2><hr>
                            <div id="searchBox">
                                {{ Form::open(['route' => 'salary.sheet', 'id' => 'monthlyDeduction', 'method' => 'GET']) }}
                                <div class="col-md-2"></div>
                                <div class="col-md-3">
                                    <div class="form-group departmentName">
                                        <label class="control-label" for="email">@lang('salary.employee_name')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
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
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.month')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'month',
                                            isset($month) ? $month : '',
                                            $attributes = ['class' => 'form-control required monthField',  'id' => 'month', 'placeholder' => __('common.month'),'autocomplete'=>'off'],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                
                                {{ Form::close() }}
                              
                            </div>
                        </div><br><br><hr>
                        <h2>Generate to All</h2>
                        <div class="row">       
                            <div id="searchBox">
                            {{ Form::open(['route' => 'salary.employeessalarydata', 'id' => 'mEmpPaySlip', 'method' => 'GET']) }}
                                <div class="col-md-2"></div>
                               
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.month')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'month',
                                            isset($month) ? $month : '',
                                            $attributes = ['class' => 'form-control required monthField',  'id' => 'salarydate','name'=>'salarydate', 'placeholder' => __('common.month'),'autocomplete'=>'off'],
                                        ) !!}
                                    </div>
                                </div>                                
                                
                                
                               
                                <div class="col-md-2">
                                    <div class="form-group"> 
                                    <!-- <input type = "hidden" class="form-control paySlipMonth required" required  name="payslipmonth" > -->
                                        <input type="submit" id="generate_msalary" style="margin-top: 25px; width: 150px;"
                                            class="btn btn-info " value="@lang('common.generate_salary')">                                            
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
