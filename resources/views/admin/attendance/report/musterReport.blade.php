@php
    use App\Model\Device;
@endphp
@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.attendance_muster_report')
@endsection
<style>
    .present {
        color: #7ace4c;
        font-weight: 700;
        cursor: pointer;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
        cursor: pointer;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
        cursor: pointer;
    }

    .bolt {
        font-weight: 700;
    }
</style>
<script>
    jQuery(function() {
        $("#attendanceMusterReport").validate();
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
                                {{ Form::open([
                                    'route' => 'attendanceMusterReport.attendanceMusterReport',
                                    'id' => 'attendanceMusterReport',
                                ]) }}
                                <br>
                                <div class="row" style="margin:0 5% 0 5%">

                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="employee_id">Student:</label>
                                            <select name="employee_id" class="form-control employee_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                        {{ $value->first_name . ' ' . $value->last_name . '(' . $value->finger_id . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.from_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.from_date')" name="from_date"
                                                value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.to_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.to_date')" name="to_date"
                                                value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 28px;width:100px"
                                                class="btn btn-instagram" value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        @if (count($results) > 0 && $results != '')
                            <h4 class="text-right">

                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadMusterAttendanceExcel/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                    Excel</a>
                            </h4>
                        @endif
                        <div class="table-responsive">
                            <table id="mustertableData" class="table table-bordered table-hover"
                                style="font-size: 12px;font-weight:400">
                                <thead>
                                    <tr class="tr_header">
                                        <th style="font-size: 13px;font-weight:500;"
                                            colspan="{{ count($monthToDate) + 6 }}" class="text-center">
                                            {{ 'Attendance Summary Report  - ' . $start_date . ' ' . ' to ' . ' ' . $end_date . '.' }}
                                        </th>
                                    </tr>
                                    <tr class="tr_header">
                                        <th style="width: 32px">@lang('common.serial')</th>
                                        <th style="width: 100px">@lang('common.name')</th>
                                        <th style="width: 100px">@lang('common.in_out_shift')</th>
                                        @foreach ($monthToDate as $head)
                                            <th>{{ $head['day'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($results) > 0)
                                        {{ $sl = null }}
                                        @foreach ($results as $fingerID => $attendance)
                                            @php
                                            @endphp
                                            <tr rowspan="5">

                                                <td>{{ ++$sl }}</td>
                                                <td>{{ $attendance[0]['fullName'] }}</td>
                                                <td>

                                                    {{ 'MS-InTime' }}
                                                    <br>
                                                    {{ 'AF-InTime' }}
                                                    <br>
                                                    {{ 'E1-InTime' }}
                                                    <br>
                                                    {{ 'E2-InTime' }}
                                                    <br>
                                                    {{ 'NS-InTime' }}
                                                    <br>
                                                </td>

                                                @foreach ($attendance as $data)
                                                    @if (strtotime($data['date']) <= strtotime(date('Y-m-d')))
                                                        <td>
                                                            {{ $data['m_in_time'] != null ? date('H:i', strtotime($data['m_in_time'])) : '-:-' }}
                                                            <br>
                                                            {{ $data['af_in_time'] != null ? date('H:i', strtotime($data['af_in_time'])) : '-:-' }}
                                                            <br>

                                                            {{ $data['e1_in_time'] != null ? date('H:i', strtotime($data['e1_in_time'])) : '-:-' }}
                                                            <br>
                                                            {{ $data['e2_in_time'] != null ? date('H:i', strtotime($data['e2_in_time'])) : '-:-' }}
                                                            <br>
                                                            {{ $data['n_in_time'] != null ? date('H:i', strtotime($data['n_in_time'])) : '-:-' }}
                                                            <br>
                                                        </td>
                                                    @endif
                                                @endforeach

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="{{ count($monthToDate) + 6 }}">
                                                No data found...
                                            </td>
                                        </tr>
                                    @endif
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
    $('#mustertableData').DataTable({
        "ordering": false,
    });

    $(document).ready(function() {
        $("#musterexcelexport").click(function(e) {
            //getting values of current time for generating the file name
            var dt = new Date();
            var day = dt.getDate();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var hour = dt.getHours();
            var mins = dt.getMinutes();
            var date = day + "." + month + "." + year;
            var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
            //creating a temporary HTML link element (they support setting file names)
            var a = document.createElement('a');
            //getting data from our div that contains the HTML table
            var data_type = 'data:application/vnd.ms-excel';
            var table_div = document.getElementById('mustertableData');
            var table_html = table_div.outerHTML.replace(/ /g, '%20');
            a.href = data_type + ', ' + table_html;
            //setting the file name
            a.download = 'SummaryReport-' + year + month + day + hour + mins + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });


    });
</script>
@endsection
