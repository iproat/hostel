<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {
        width: 35%;
        margin: 0 auto;
    }

    table,
    td,
    th {
        font-size: 10px;
        border: 1px solid black;
    }

    td {
        font-size: 8px;
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .present {
        color: #7ace4c;
        font-weight: 700;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
    }

    .bolt {
        font-weight: 700;
    }
</style>

<body>
    <div class="printHead">
        <p style="margin-left: 32px;margin-top: 10px"><b>@lang('attendance.attendance_summary_report')</b></p>
    </div>
    <div class="container">
        @php
            $colCount = count($monthToDate) + count($leaveTypes) + 3;
        @endphp
        <b aria-colspan="{{ $colCount }}">Month : </b>{{ $month }}
        <div class="table-responsive" style="font-size: 12px">
            <table id="" class="table table-bordered table-striped table-hover" style="font-size: 12px">
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
                        <th>{{ $monthName }}</th>
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
                        <th>@lang('employee.designation')</th>
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
                            <td>{{ $key . '(' . $value[0]['finger_id'] . ')' }}</td>
                            <td>{{ $value[0]['designation_name'] }}</td>
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
                </tbody>
            </table>
        </div>
