@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.attendance_summary_report')
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
        $("#attendanceSummaryReport").validate();
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
                                    'route' => 'attendanceSummaryReport.attendanceSummaryReport',
                                    'id' => 'attendanceSummaryReport',
                                ]) }}
                                <div class="col-md-3"></div>

                                <div class="col-md-4">
                                    <label class="control-label" for="email">@lang('common.month')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control monthField required" readonly
                                            placeholder="@lang('common.month')" name="month"
                                            value="@if (isset($month)) {{ $month }}@else {{ date(' Y-m') }} @endif">
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
                        @if (count($results) > 0)
                            <h4 class="text-right" hidden>
                                @if (isset($month))
                                    <a target="_blank" class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadAttendanceSummaryReport/' . $month) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true" hidden></i>
                                        @lang('common.download')
                                        PDF</a>
                                @else
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadAttendanceSummaryReport/' . date('Y-m')) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true" hidden></i>
                                        @lang('common.download')
                                        PDF</a>
                                @endif
                            </h4>
                        @endif
                        @if (count($results) > 0 && $results != '')
                            <h4 class="text-right">
                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadSummaryAttendanceExcel/?month=' . $month) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                    Excel</a>
                            </h4>
                        @endif
                        <div class="table-responsive">
                            <table id="" class="table table-bordered table-striped table-hover"
                                style="font-size: 12px">
                                <thead>
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('common.year')</th>
                                        <th colspan="0" class="totalCol">@lang('common.month')</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            @if (isset($month))
                                                @php
                                                    
                                                    $exp = explode('-', $month);
                                                    echo $exp[0];
                                                @endphp
                                            @else
                                                {{ date('Y') }}
                                            @endif
                                        </th>
                                        {{-- <th>{{ $monthName }}</th> --}}
                                        @foreach ($monthToDate as $head)
                                            <th>{{ $head['day_name'] }}</th>
                                        @endforeach
                                        <th>@lang('attendance.day_of_worked')</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#</td>
                                        <th>@lang('common.name')</th>
                                        {{-- <th>@lang('common.branch')</th> --}}
                                        @foreach ($monthToDate as $head)
                                            <th>{{ $head['day'] }}</th>
                                        @endforeach
                                        <th>#</th>

                                    </tr>

                                    @php
                                        $sl = null;
                                        $totalPresent = 0;
                                        $leaveData = [];
                                        $totalCol = 0;
                                        $totalWorkHour = 0;
                                        $totalGovDayWorked = 0;
                                    @endphp
                                    @foreach ($results as $key => $value)
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $value[0]['fullName'] . '(' . $key . ')' }}</td>
                                            {{-- <td>{{ $value[0]['branch_name'] }}</td> --}}
                                            @foreach ($value as $v)
                                                @php
                                                    // dd($v);
                                                    if ($sl == 1) {
                                                        $totalCol++;
                                                    }
                                                    if ($v['attendance_status'] == 'present') {
                                                        $totalPresent++;
                                                        // $totalGovDayWorked++;
                                                        echo "<td><span style='color:#7ace4c ;font-weight:bold'>P</span></td>";
                                                    } elseif ($v['attendance_status'] == 'absence') {
                                                        echo "<td><span style='color:#f33155 ;font-weight:bold'>A</span></td>";
                                                    } elseif ($v['attendance_status'] == 'leave') {
                                                        $leaveData[$key][$v['leave_type']][] = $v['leave_type'];
                                                        echo "<td><span style='color:#41b3f9 ;font-weight:bold'>L</span></td>";
                                                    } elseif ($v['attendance_status'] == 'holiday') {
                                                        echo "<td><span style='color:turquoise ;font-weight:bold'>H</span></td>";
                                                    } else {
                                                        echo '<td></td>';
                                                    }
                                                @endphp
                                            @endforeach
                                            <td><span class="bolt">{{ $totalPresent }}</span></td>
                                            
                                            @php
                                                $totalPresent = 0;
                                                $totalGovDayWorked = 0;
                                                
                                            @endphp
                                        </tr>
                                    @endforeach
                                    {{-- <script>
                                        {!! "$('.totalCol').attr('colspan',$totalCol+3);" !!}
                                    </script> --}}
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
