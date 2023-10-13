@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.daily_attendance')
@endsection
<style>
    span {
        font-weight: 200
    }
</style>
<script>
    jQuery(function() {
        $("#dailyAttendanceReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
                        <div id="searchBox">
                            <div class="col-md-2"></div>
                            {{ Form::open([
                                'route' => 'dailyAttendance.dailyAttendance',
                                'id' => 'dailyAttendanceReport',
                                'class' => 'form-horizontal',
                            ]) }}
                            <div class="form-group mx-auto">
                                <div class="col-md-2" style="margin-left: 24px;">
                                    <div class="form-group">
                                        <label class="control-label" for="email">@lang('common.date')<span
                                                class="validateRq">*</span>:</label>
                                        <input type="text" class="form-control dateField" style="height: 35px;"
                                            required readonly placeholder="@lang('common.date')" id="date"
                                            name="date"
                                            value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
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
                        <hr>

                        @if (count($results) > 0 && $results != '')
                            <h4 class="text-right">
                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadDailyAttendanceExcel/?date=' . $date . '&attendance_status=' . $attendance_status) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                    Excel</a>

                            </h4>
                        @endif



                        <div id="btableData">
                            <div class="table-responsive">
                                <table id="" class="table table-bordered" style="font-size: 12px">
                                    <thead class="tr_header bg-title">

                                        <tr>
                                            <th style="width:50px;">@lang('common.serial')</th>
                                            <th style="font-size:12px;">@lang('common.date')</th>
                                            <th style="font-size:12px;width:200px;">Student Name</th>
                                            <th style="font-size:12px;">M-S In/Out Status</th>
                                            <th style="font-size:12px;">Af-S In/Out Status</th>
                                            <th style="font-size:12px;">Eve1-S In/Out Status</th>
                                            <th style="font-size:12px;">Eve2 In/Out Status</th>
                                            <th style="font-size:12px;">N-S In/Out Status</th>
                                            <th style="font-size:12px;width:350px;">Punch Records</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $inc = 1;
                                        @endphp
                                        @forelse ($results as $key => $value)
                                            @if ($attendance_status == 1)
                                                @if (
                                                    $attendance_status == $value->m_status ||
                                                        $attendance_status == $value->af_status ||
                                                        $attendance_status == $value->e1_status ||
                                                        $attendance_status == $value->e2_status ||
                                                        $attendance_status == $value->n_status)
                                                    <tr>
                                                        <td style="font-size:12px;">{{ $inc++ }}</td>
                                                        <td style="font-size:12px;">{{ $value->date ?? '-' }}</td>
                                                        <td style="font-size:12px;">{{ $value->fullName }}</td>

                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->m_in_time ?? '-' }}</span>
                                                                <br />
                                                                @if ($attendance_status == $value->m_status)
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value->m_status == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value->m_status == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">

                                                                <span
                                                                    style="font-size:12px;">{{ $value->af_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->af_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->af_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->e1_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->e1_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->e1_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->e2_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->e2_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->e2_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->n_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->n_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->n_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>

                                                            </span>
                                                        </td>

                                                        <td style="font-size:12px;">{{ $value->in_out_time }}</td>
                                                    </tr>
                                                @endif
                                            @endif
                                            @if ($attendance_status == 2)
                                                @if (
                                                    $attendance_status == $value->m_status &&
                                                        $attendance_status == $value->af_status &&
                                                        $attendance_status == $value->e1_status &&
                                                        $attendance_status == $value->e2_status &&
                                                        $attendance_status == $value->n_status)
                                                    <tr>
                                                        <td style="font-size:12px;">{{ $inc++ }}</td>
                                                        <td style="font-size:12px;">{{ $value->date ?? '-' }}</td>
                                                        <td style="font-size:12px;">{{ $value->fullName }}</td>

                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->m_in_time ?? '-' }}</span>
                                                                <br />
                                                                @if ($attendance_status == $value->m_status)
                                                                    <span
                                                                        style="font-size: 12px; font-weight: bold; color: {{ $value->m_status == 1 ? '#487200' : '#b10000' }}">
                                                                        {{ $value->m_status == 1 ? 'Present' : 'Absent' }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">

                                                                <span
                                                                    style="font-size:12px;">{{ $value->af_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->af_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->af_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->e1_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->e1_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->e1_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->e2_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->e2_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->e2_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-medium">
                                                                <span
                                                                    style="font-size:12px;">{{ $value->n_in_time ?? '-' }}</span>
                                                                <br />
                                                                <span
                                                                    style="font-size: 12px; font-weight: bold; color: {{ $value->n_status == 1 ? '#487200' : '#b10000' }}">
                                                                    {{ $value->n_status == 1 ? 'Present' : 'Absent' }}
                                                                </span>

                                                            </span>
                                                        </td>

                                                        <td style="font-size:12px;">{{ $value->in_out_time }}</td>
                                                    </tr>
                                                @endif
                                            @endif

                                        @empty
                                            <tr>
                                                <td colspan="19">@lang('common.no_data_available') !</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
@endsection
