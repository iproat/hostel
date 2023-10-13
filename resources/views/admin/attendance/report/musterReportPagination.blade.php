<div class="table-responsive">
    <table id="mustertableData" class="table table-bordered table-striped table-hover"
        style="font-size: 12px;font-weight:400">
        <thead>
            <tr class="tr_header">
                <th style="font-size: 13px;font-weight:500;" colspan="{{ count($monthToDate) + 6 }}" class="text-center">
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
                                    {{ $data['m_in_time'] != null ? date('H:i', strtotime($data['m_in_time'])) : '-' }}
                                    <br>
                                    {{ $data['af_in_time'] != null ? date('H:i', strtotime($data['af_in_time'])) : '-' }}
                                    <br>

                                    {{ $data['e1_in_time'] != null ? date('H:i', strtotime($data['e1_in_time'])) : '-' }}
                                    <br>
                                    {{ $data['e2_in_time'] != null ? date('H:i', strtotime($data['e2_in_time'])) : '-' }}
                                    <br>
                                    {{ $data['n_in_time'] != null ? date('H:i', strtotime($data['n_in_time'])) : '-' }}
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

@section('page-scripts')
    <script>
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
                a.download = 'AttendanceSummaryReport-' + date + '.xls';
                //triggering the function
                a.click();
                //just in case, prevent default behaviour
                e.preventDefault();
            });
        });
    </script>
@endsection
