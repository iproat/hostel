<div class="table-responsive">

    <table id="myDataTable" class="table table-bordered">

        <thead class="tr_header">
            <tr>
                <th>Name</th>
                <th>Employee Id</th>
                <th>@lang('attendance.in_time')</th>
                <th>@lang('attendance.out_time')</th>
                <th>@lang('attendance.updated_by')</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($results as $key => $value)
            @php
            // dd($value);
            @endphp
                <tr class="{{ $value->finger_print_id }}">
                    <td style="vertical-align:center;">
                        {{ ucwords(trim($value->employee->first_name . ' ' . $value->employee->last_name)) }}
                    </td>
                    <td style="vertical-align:center;">
                        {{ $value->finger_print_id }}
                    </td>
                    <td>
                        <div class="input-group">
                            <b hidden>{{ $value->in_time ? date('Y-m-d H:i', strtotime($value->in_time)) : '' }}</b>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input class="form-control intime{{ $value->finger_print_id }}" type="datetime-local"
                                placeholder="@lang('attendance.in_time')" name="in_time" data-id="{{ $value->finger_print_id }}"
                                value="{{ $value->in_time ? date('Y-m-d H:i', strtotime($value->in_time)) : '' }}"
                                @if (!$value->in_time) min="{{ date('Y-m-d', strtotime(dateConvertFormToDB($_REQUEST['date']))) . 'T00:00' }}"
                                max="{{ date('Y-m-d', strtotime('+36 hours', strtotime(dateConvertFormToDB($_REQUEST['date'])))) . 'T00:00' }}" @endif>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <b hidden>{{ $value->out_time ? date('Y-m-d H:i', strtotime($value->out_time)) : '' }}</b>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input class="form-control outtime{{ $value->finger_print_id }}" type="datetime-local"
                                placeholder="@lang('attendance.out_time')" name="out_time" data-id="{{ $value->finger_print_id }}"
                                value="{{ $value->out_time ? date('Y-m-d H:i', strtotime($value->out_time)) : '' }}"
                                @if (!$value->out_time) min="{{ date('Y-m-d', strtotime(dateConvertFormToDB($_REQUEST['date']))) . 'T00:00' }}"
                                max="{{ date('Y-m-d', strtotime('+36 hours', strtotime(dateConvertFormToDB($_REQUEST['date'])))) . 'T00:00' }}" @endif>
                        </div>
                    </td>

                    <td style="vertical-align:center;">
                        @if (isset($value->updatedBy) && $value->updatedBy != null)
                            {{ ucwords(trim($value->updatedBy->first_name . ' ' . $value->updatedBy->last_name)) }}
                            {{ '@ ' . date('Y-m-d h:i A', strtotime($value->updated_at)) }}
                        @else
                            {{ 'NA @' }}
                            {{ '0000-00-00 00:00:00' }}
                        @endif
                    </td>

                    <td style="vertical-align:center;">
                        @if (count($results) > 0)
                            <a type="submit" href="{!! route('manualAttendance.individualReport', [
                                'finger_print_id' => $value->finger_print_id,
                            ]) !!}" data-token="{!! csrf_token() !!}"
                                data-id="{!! $value->finger_id !!}" class="generateReportIndividually">
                                <button class="btn btn-instagram btn-sm" title="save" id="rptSave"><i
                                        class="fa fa-save"></i></button></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
