@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.employee_attendance')
@endsection
<style>
    .departmentName {
        position: relative;
    }

    #department_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }
</style>
<script>
    jQuery(function() {
        $("#employeeAttendance").validate();
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
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">ï¿½</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">ï¿½</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'manualAttendance.filter', 'id' => 'employeeAttendance', 'method' => 'GET']) }}
                                <div class="col-md-2"></div>
                                <div class="col-md-3">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.name')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if (isset($_REQUEST['employee_id'])) @if ($_REQUEST['employee_id'] == $value->employee_id) {{ 'selected' }} @endif
                                                    @endif
                                                    >{{ $value->first_name . ' ' . $value->last_name . ' (' . $value->finger_id . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.date')" name="date"
                                            value="@if (isset($_REQUEST['date'])) {{ $_REQUEST['date'] }}@else{{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
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
                        </div>
                        <hr>
                        @if (isset($attendanceData))
                            {{ Form::open(['route' => 'manualAttendance.store', 'id' => 'employeeAttendance']) }}

                            <input type="hidden" name="employee_id" value="{{ $_REQUEST['employee_id'] }}">
                            <input type="hidden" name="date" value="{{ $_REQUEST['date'] }}">

                            <div class="table-responsive" style="height: 50vh">
                                <table class="table table-bordered" style="position: absolute;">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('employee.finger_print_no')</th>
                                            <th>@lang('common.employee_name')</th>
                                            <th>@lang('attendance.in_time')</th>
                                            <th>@lang('attendance.out_time')</th>
                                            <th>@lang('attendance.updated_by')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($attendanceData) > 0)
                                            @foreach ($attendanceData as $value)
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ $value->finger_id }}</td>
                                                    <td>{{ $value->fullName }}</td>
                                                    <td style="width: 300px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                            <div class="bootstrap-datetimepicker">
                                                                <input type="hidden" name="finger_print_id[]"
                                                                    value="{{ $value->finger_id }}">
                                                                <input class="form-control" id="datetimepicker1"
                                                                    type="text" placeholder="@lang('attendance.in_time')"
                                                                    name="inTime[]" value="{{ $value->inTime }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="width: 300px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                            <div class="bootstrap-datetimepicker">
                                                                <input class="form-control" id="datetimepicker2"
                                                                    type="text" placeholder="@lang('attendance.out_time')"
                                                                    name="outTime[]" value="{{ $value->outTime }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $employee = App\Model\Employee::where('employee_id', $value->updatedBy)
                                                                ->select('first_name', 'last_name')
                                                                ->first();
                                                        @endphp
                                                        @if ($employee && $value->updatedAt && $value->updatedAt != null)
                                                            {{ $employee->first_name . ' ' . $employee->last_name }}
                                                            <br>
                                                            {{ date('Y-m-d h:i A', strtotime($value->updatedAt)) }}
                                                        @else
                                                            {{ 'NA' }} <br>
                                                            {{ '0000-00-00 00:00:00' }}
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">@lang('attendance.no_data_available')</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (count($attendanceData) > 0)
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-check"></i> @lang('common.save')</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    //  $(document).on("focus", ".datetimepicker", function() {
    //     $(this).datetimepicker();
    // });

    var StartDate = $('.dateField').val();
    $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',

    });
</script>
@endsection
