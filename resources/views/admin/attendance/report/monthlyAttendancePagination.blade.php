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
        border: 1px solid black;
        font-weight: 500;
    }

    td {
        padding: 5px;
    }

    th {
        padding: 5px;
    }
</style>
<div class="container">
    <table class="table table-bordered" style="font-size: 12px;">
        <thead class="tr_header">
            <tr>
                <th colspan="6">@lang('attendance.monthly_attendance_report')</th>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="2"><b>@lang('common.name') &nbsp;</b></td>
                <td colspan="1"><b>@lang('common.from_date') &nbsp;</b></td>
                <td colspan="1"><b>@lang('common.to_date') &nbsp;</b></td>

            </tr>
            <tr>
                <td colspan="2">{{ $employee_name }}</td>
                <td>{{ $from_date }}</td>
                <td>{{ $to_date }}</td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
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
</div>
