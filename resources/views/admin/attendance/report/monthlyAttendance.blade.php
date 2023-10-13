@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.monthly_attendance_report')
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
</style>
<script>
    jQuery(function() {
        $("#monthlyAttendance").validate();
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

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'monthlyAttendance.monthlyAttendance', 'id' => 'monthlyAttendance']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">Student<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if (@$value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name }} {{ $value->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                    </div>
                                </div>
                                @php
                                    $listStatus = [
                                        '1' => 'Present',
                                        '2' => 'Absent',
                                    ];
                                @endphp
                                <div class="col-md-2" style="margin-left:24px;">
                                    <div class="form-group">
                                        <label class="control-label" for="email">@lang('common.status'):<span
                                                class="validateRq">*</span></label>
                                        <select name="attendance_status"
                                            class="form-control attendance_status  select2 required">
                                            <option value="">--- @lang('common.please_select') ---</option>
                                            @foreach ($listStatus as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($key == $attendance_status) {{ 'selected' }} @endif>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
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

                        @if (count($results) > 0 && $results != '')
                            <h4 class="text-right">
                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadMonthlyAttendanceExcel/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date . '&attendance_status=' . $attendance_status) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                    Excel</a>
                            </h4>
                        @endif

                        @if ($results != '')
                            <table class="table table-bordered" style="font-size: 12px">
                                <thead class="tr_header">
                                    <tr>
                                        <th style="width:100px;">@lang('common.serial')</th>
                                        <th>@lang('common.date')</th>
                                        <th>M Status</th>
                                        <th>AF Status</th>
                                        <th>E1 Status</th>
                                        <th>E2 Status</th>
                                        <th>N Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($results) > 0)
                                        {{ $serial = null }}
                                        @forelse($results as $value)
                                            @if ($attendance_status == 1)
                                                @if (
                                                    $attendance_status == $value['m_status'] ||
                                                        $attendance_status == $value['af_status'] ||
                                                        $attendance_status == $value['e1_status'] ||
                                                        $attendance_status == $value['e2_status'] ||
                                                        $attendance_status == $value['n_status']
                                                )
                                                    <tr>
                                                        <td style="width:100px;">{{ ++$serial }}</td>
                                                        <td>{{ $value['date'] }}</td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['m_in_time'] != '')
                                                                        {{ $value['m_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['m_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['m_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['m_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['af_in_time'] != '')
                                                                        {{ $value['af_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['af_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['af_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['af_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['e1_in_time'] != '')
                                                                        {{ $value['e1_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />
                                                                @if ($attendance_status == $value['e1_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['e1_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['e1_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['e2_in_time'] != '')
                                                                        {{ $value['e2_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['e2_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['e2_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['e2_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['n_in_time'] != '')
                                                                        {{ $value['n_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['n_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['n_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['n_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @endif
                                            @if ($attendance_status == 2)
                                                @if (
                                                    $attendance_status == $value['m_status'] &&
                                                        $attendance_status == $value['af_status'] &&
                                                        $attendance_status == $value['e1_status'] &&
                                                        $attendance_status == $value['e2_status'] &&
                                                        $attendance_status == $value['n_status']
                                                )
                                                    <tr>
                                                        <td style="width:100px;">{{ ++$serial }}</td>
                                                        <td>{{ $value['date'] }}</td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['m_in_time'] != '')
                                                                        {{ $value['m_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['m_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['m_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['m_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['af_in_time'] != '')
                                                                        {{ $value['af_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['af_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['af_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['af_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['e1_in_time'] != '')
                                                                        {{ $value['e1_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />
                                                                @if ($attendance_status == $value['e1_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['e1_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['e1_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['e2_in_time'] != '')
                                                                        {{ $value['e2_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['e2_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['e2_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['e2_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span style="font-size:12px;">
                                                                    @if ($value['n_in_time'] != '')
                                                                        {{ $value['n_in_time'] }}
                                                                    @else
                                                                        {{ '--' }}
                                                                    @endif
                                                                </span>
                                                                <br />

                                                                @if ($attendance_status == $value['n_status'])
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value['n_status'] == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value['n_status'] == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @endif

                                            @empty
                                                <tr>
                                                    <td colspan="5">@lang('common.no_data_available') !</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                </tbody>
                            </table>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
