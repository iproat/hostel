@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.attendance_record')
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
                        {{-- <p class="text-center">
                            <b style="font-size: 12px"><span style="color: green">Attendance Devices -
                                    Green,</span>
                                <span style="color: blue">Mobile Devices - Blue,</span>
                                <span style="color: red">Manual Correction - Red,</span>
                                <span style="color: orange">Access Device - Orange.</span>
                            </b>
                        </p> --}}
                        <div class="row">
                            <div id="searchBox">

                                {{ Form::open(['route' => 'attendanceRecord.attendanceRecord', 'id' => 'attendanceRecord']) }}

                                <div class="form-group">

                                    <div class="col-md-2"></div>

                                    {{-- <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label" for="employee_id">@lang('common.employee'):</label>
                                            <select class="form-control employee_id select2" name="employee_id">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}">{{ $value->first_name }}
                                                        {{ $value->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-3">
                                        @php
                                            $devices = allDevices();
                                        @endphp
                                        <div class="form-group" hidden>
                                            <label class="control-label" for="device_name">@lang('common.device'):</label>
                                            <select name="device_name" class="form-control device_name select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($devices as $value)
                                                    <option value="{{ $value->device_name }}"
                                                        @if ($value->device_name == $device_name) {{ 'selected' }} @endif>
                                                        {{ $value->device_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-sm-3" style="margin-left:24px;">
                                        <div class="form-group">
                                            <label class="control-label" for="date">@lang('common.date')<span
                                                    class="validateRq">*</span>:</label>
                                            <input type="text" class="form-control dateField" style="height: 35px;"
                                                readonly placeholder="@lang('common.date')" id="date" name="date"
                                                value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif"
                                                required>
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
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered" style="font-size: 12px">
                                <thead class="tr_header">
                                    <tr>
                                        <th style="width:80px;">@lang('common.serial')</th>
                                        <th>Employee Id</th>
                                        <th>@lang('common.name')</th>
                                        <th>DateTime</th>
                                        <th>In/Out</th>
                                        <th hidden>Device Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ $serial = null }}
                                    @foreach ($results as $value)
                                        @php
                                            $employee = App\Model\Employee::where('finger_id', $value['ID'])
                                                ->select('first_name', 'last_name')
                                                ->first();
                                            $deviceSerialNo = ['BRM9193360148', 'BRM9193360137', 'BRM9193360058', 'BRM9193360034', 'BRM9193360025', 'BRM9192960031', 'BRM9193360059', 'BRM9191060473', 'BRM9193360057', 'BRM9193360055'];
                                            $mobile = ['Mobile'];
                                            $manual = ['Manual'];
                                            $attReport = in_array($value['devuid'], $deviceSerialNo);
                                            $attManualReport = in_array($value['devuid'], $manual);
                                            $attMobileReport = in_array($value['devuid'], $mobile);
                                            // echo "<pre>";
                                            // var_dump ($attReport);
                                            // echo "</pre>";
                                            $color = '#000';
                                            
                                        @endphp
                                        @if (isset($employee))
                                            <tr>
                                                <td style="width:100px;">
                                                    <p style="color: black">{{ ++$serial }}</p>
                                                </td>
                                                <td>
                                                    <p style="color: black">{{ $value['ID'] }} </p>
                                                </td>
                                                <td>
                                                    <p style="color: black">
                                                        {{ $employee['first_name'] . ' ' . $employee['last_name'] }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p style="color: black">{{ $value['datetime'] }} </p>
                                                </td>
                                                <td>
                                                    <p style="color: black">{{ $value['type'] }} </p>
                                                </td>
                                                <td hidden>
                                                    <p style="color: black">
                                                        {{ $value['device_name'] != '' ? $value['device_name'] : '-' }}
                                                    </p>
                                                </td>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
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
