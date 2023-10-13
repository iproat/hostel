<div>
    <div class="table-responsive">
        <table class="table table-bordered" style="font-size: 12px">
            <thead class="tr_header bg-title">
                <tr>
                    <th style="width:50px;">@lang('common.serial')</th>
                    <th style="font-size:12px;">@lang('common.date')</th>
                    <th style="font-size:12px;width:200px;">@lang('common.employee_name')</th>
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
                                        <span style="font-size:12px;">{{ $value->m_in_time ?? '-' }}</span>
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

                                        <span style="font-size:12px;">{{ $value->af_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->af_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->af_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->e1_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->e1_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->e1_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->e2_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->e2_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->e2_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->n_in_time ?? '-' }}</span>
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
                                        <span style="font-size:12px;">{{ $value->m_in_time ?? '-' }}</span>
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

                                        <span style="font-size:12px;">{{ $value->af_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->af_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->af_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->e1_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->e1_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->e1_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->e2_in_time ?? '-' }}</span>
                                        <br />
                                        <span
                                            style="font-size: 12px; font-weight: bold; color: {{ $value->e2_status == 1 ? '#487200' : '#b10000' }}">
                                            {{ $value->e2_status == 1 ? 'Present' : 'Absent' }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium">
                                        <span style="font-size:12px;">{{ $value->n_in_time ?? '-' }}</span>
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
