@extends('admin.master')
@section('content')
@section('title')
    @lang('dashboard.dashboard')
@endsection
<style>
    .dash_image {

        width: 60px;
    }

    .my-custom-scrollbar {
        position: relative;
        height: 280px;
        overflow: auto;
    }

    .table-wrapper-scroll-y {
        display: block;
    }

    tbody {
        display: block;
        height: 300px;
        overflow: auto;
    }

    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    thead {
        width: calc(100% - 1em)
    }

    .leaveApplication {
        overflow-x: hidden;
        height: 210px;
    }

    .noticeBord {
        overflow-x: hidden;
        height: 210px;
    }

    .preloader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('../images/timer.gif') 50% 50% no-repeat rgb(249, 249, 249);
        opacity: .8;
    }
</style>
<script>
    function loadingAjax(div_id) {
        $("#" + div_id).html('<img src="ajax-loader.gif"> saving...');
        $.ajax({
            type: "GET",
            url: "script.php",
            data: "name=John&id=28",
            success: function(msg) {
                $("#" + div_id).html(msg);
            }
        });
    }
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">TOTAL STUDENTS</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/employee.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-success"></i> <span
                            class="counter text-success">{{ $totalEmployee }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_department')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/department.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span
                            class="counter text-purple">{{ $totalDepartment }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12" hidden>
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_present')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/present.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-info"></i> <span
                            class="counter text-info">{{ $totalAttendance }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12" hidden>
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_absent')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/absent.png') }}">
                    </li>
                    <li class="text-right"><a href="#"><i id="absentDetail"
                                class="ti-arrow-down text-danger"></i></a>
                        <span class="counter text-danger">{{ $totalAbsent }}</span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12" style="display:inline-table;">
                <div class="panel">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>
                        @lang('dashboard.today_attendance')
                    </div>
                    <div class="table-responsive scroll-hide">
                        <table class="table table-hover table-borderless manage-u-table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>@lang('dashboard.photo')</th>
                                    <th>Employee Name</th>
                                    <th>Datetime</th>
                                    <th>MS-Status</th>
                                    <th>AF-Status</th>
                                    <th>E1-Status</th>
                                    <th>E2-Status</th>
                                    <th>N-Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($attendanceData) > 0)
                                    {{ $dailyAttendanceSl = null }}
                                    @foreach ($attendanceData as $dailyAttendance)
                                        <tr>
                                            <td class="text-center">{{ ++$dailyAttendanceSl }}</td>
                                            <td>
                                                @if (isset($dailyAttendance->photo) && $dailyAttendance->photo != '')
                                                    <img height="40" width="40" src="{!! asset('uploads/employeePhoto/' . $dailyAttendance->photo) !!}"
                                                        alt="user-img" class="img-circle">
                                                @else
                                                    <img height="40" width="40" src="{!! asset('admin_assets/img/default.png') !!}"
                                                        alt="user-img" class="img-circle">
                                                @endif
                                            </td>
                                            <td>{{ $dailyAttendance->fullName }}</td>
                                            <td>{{ $dailyAttendance->date }}</td>
                                            <td>{{ $dailyAttendance->m_in_time ?? '-' }}</td>
                                            <td>{{ $dailyAttendance->af_in_time ?? '-' }}</td>
                                            <td>{{ $dailyAttendance->e1_in_time ?? '-' }}</td>
                                            <td>{{ $dailyAttendance->e2_in_time ?? '-' }}</td>
                                            <td>{{ $dailyAttendance->n_in_time ?? '-' }}</td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">@lang('common.no_data_available')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="preloaders" class="preloader"></div>


</div>
@endsection

@section('page_scripts')
<script type="text/javascript">
    document.onreadystatechange = function() {
        switch (document.readyState) {
            case "loading":
                window.documentLoading = true;
                break;
            case "complete":
                window.documentLoading = false;
                break;
            default:
                window.documentLoading = false;
        }
    }
    $(document).ready(function() {
        var currentDate = new Date().toISOString().split('T')[0];
        var actionTo = "{{ URL::to('generateReport') }}";
        $.ajax({
            url: actionTo,
            type: 'GET',
            data: {
                from_date: currentDate,
                to_date: currentDate
            },
            success: function(response) {
                swal("Refreshed", "Your data is safe .", "success");
            },
            error: function(xhr, status, error) {
                swal("error occured", "Your data is safe .", "error");
            }
        });
    });

    function loading($bool) {
        // $("#preloaders").fadeOut(1000);
        if ($bool == true) {
            $.toast({
                heading: 'success',
                text: 'Processing Please Wait !',
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 1
            });
            window.setTimeout(function() {
                location.reload()
            }, 3000);
        }
        $("#preloaders").fadeOut(1000);
    }


    // if (window.documentLoading = true) {
    //     $("#preloaders").fadeOut(1000);
    // }

    // $(document).on('click', '.loading', function() {
    //     $("#preloaders").fadeOut(1000);
    // });
</script>

<link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>
<script type="text/javascript"></script>
@endsection
