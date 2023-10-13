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

    /* @if (count($attendanceData) > 3)
    */
    .tbody {
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
    @media only screen and (max-width: 600px) {
  body {
    font-size: 10px !important;
    font-weight:800px !important;

  }
  .table{
    width:100%;
  }
  img  
  {
     width:35px !important;
     vertical-align: left !important;

  }
  .white-box {
    background: #fff;
    padding: 9px;
    margin-bottom: 30px;
    /* width: 111%; */
     
}
.hidden{
    display:none;
    
}
.hidden{
    visibilty:hidden;
    
}
}
.branch{
    /* text-align:center !important; */
    padding-top:10px !important;    
    
}
    /*
@endif
    */

    /* @if (count($leaveApplication) >= 1)
    */
    .leaveApplication {
        overflow-x: hidden;
        height: 210px;
    }

    /*
@endif
    */

    /* @if (count($notice) >= 1)
    */
    .noticeBord {
        overflow-x: hidden;
        height: 210px;
    }

    /*
@endif
    */
</style>

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
        <div class="col-md-12">
    {!! Form::open(['url' => 'dashboard', 'class' => 'form-horizontal new-lg-form', 'id' => 'branchEmployees','method'=>'POST']) !!}
                             
    @if ($errors->any())
						<div class="alert alert-danger alert-dismissible" role="alert">
							@foreach ($errors->all() as $error)
							<strong>{!! $error !!}</strong><br>
							@endforeach
						</div>
						@endif

						@if (session()->has('error'))
						<div class="alert alert-danger">
							<p>{!! session()->get('error') !!}</p>
						</div>
						@endif

						@if (session()->has('success'))
						<div class="alert alert-success">
							<p>{!! session()->get('success') !!}</p>
						</div>
						@endif
                          
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title"> @lang('dashboard.total_employee') </h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/employee.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-success"></i> <span class="counter text-success">{{ $totalEmployee }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_department')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/department.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span class="counter text-purple">{{ $totalDepartment }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_present')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/present.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-info"></i> <span class="counter text-info">{{ $totalAttendance }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_absent')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/absent.png') }}">
                    </li>
                    <li class="text-right"><a href="#"><i id="absentDetail" class="ti-arrow-down text-danger"></i></a> <span class="counter text-danger">{{ $totalAbsent }}</span></li>
                </ul>

            </div>
        </div>
    </div>
    

<div class="col-md-12 col-lg-12 col-sm-12" style="display:inline-table;">
 
    <div class="white-box">                    
        <div class="box-title"> @lang('dashboard.today_attendance') </div>
       
    <div class="row inline-block">
    <!-- <div class= "col-sm-1"></div>        -->
        <div class= "col-sm-6" style="position:relative;left:30px;">  
        <label for="exampleInput" style="position:relative; "> @lang('common.branch') </label>
                <select name="branch_id" class="form-control branch_id select2" style="width:70%;" required onchange="this.form.submit()">
                    <option value="">--- @lang('employee.select_branch') ---</option>
                    @foreach ($branchList as $value)
                        @if(isset($_POST['branch_id']))
                            <option value="{{ $value->branch_id  }}" @if ($value->branch_id  == $_POST['branch_id']) {{ 'selected' }} @endif>
                                {{ $value->branch_name }}
                            </option>
                        @else
                            <option value="{{ $value->branch_id  }}" >
                                {{ $value->branch_name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <!-- <button class="btn btn-info text-uppercase waves-effect waves-light" type="submit">Submit</button>   -->
        </div>
        <div class= "col-sm-2" style="position:relative;left:5px;">
         
        <div class= "col-sm-6"></div>
    </div>
        <div class=" scroll-hide" style="padding: 4px;">
            <table class="table table-hover table-borderless table-responsive-xl text-left" >
                <thead class="text-left">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:15%;">@lang('dashboard.photo')</th>
                        <th style="width:20%;">@lang('common.name')</th>
                        <th style="width:15%;">@lang('dashboard.morning_in_out_time')</th> 
                        <th style="width:15%;">@lang('dashboard.evening_in_out_time')</th> 
                        <th style="width:15%;">@lang('dashboard.morning_late')</th> 
                        <th style="width:15%;">@lang('dashboard.evening_late')</th>
                    </tr>
                </thead>
                <tbody>
                 
                    @if (count($attendanceData) > 0)
                    {{ $dailyAttendanceSl = null }}
                    @foreach ($attendanceData as $key => $dailyAttendance)
                    @php 
                    $fdevice_name = App\Model\Device::where('id',$dailyAttendance->first_device)->first();
                    $sdevice_name = App\Model\Device::where('id',$dailyAttendance->second_device)->first();
                    @endphp
                     
                    @if(isset($_POST['branch_id'])) 
                    @if($dailyAttendance->branch_id == $_POST['branch_id'])
                    <tr class="{!! $dailyAttendance->employee_id !!}">
                        <td style="width:5%;">{{ ++$dailyAttendanceSl }}</td>
                        <td style="width:15%;">
                            @if ($dailyAttendance->photo != '')
                            <img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/' . $dailyAttendance->photo) !!}" alt="user-img" class="img-circle">
                            @else
                            <img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">
                            @endif
                        </td>
                        <td style="width:20%;">
                            <span>{{ $dailyAttendance->fullName }}</span>
                            <br /><span class="text-muted">{{ $dailyAttendance->branch_name  }}</span>
                            <br /><span class="text-muted">ID:{{ $dailyAttendance->finger_id }}</span>  
                        </td>
                        <td style="width:15%;">
                            <?php
                            if ($dailyAttendance->mrng_in_time != '') {
                                $mrng_intime= date("h:ia", strtotime($dailyAttendance->mrng_in_time));
                            } else {
                                $mrng_intime= '00:00';
                            }
                             
                            if ($dailyAttendance->mrng_out_time != '') {
                                $mrng_outtime= date("h:ia", strtotime($dailyAttendance->mrng_out_time));
                            } else {
                                $mrng_outtime='00:00';
                            }
                            echo $mrng_intime.'-'.$mrng_outtime;
                            ?>
                            <br /><span class="text-muted">M-Device:@if($fdevice_name){{ $fdevice_name->name }} @endif</span>
                        </td>
                        <td style="width:15%;">
                            <?php
                            if ($dailyAttendance->eve_in_time != '') {
                                $eve_intime = date("h:ia", strtotime($dailyAttendance->eve_in_time));
                            } else {
                                $eve_intime = '00:00';
                            }
                             
                            if ($dailyAttendance->eve_out_time != '') {
                                $eve_outtime = date("h:ia", strtotime($dailyAttendance->eve_out_time));
                            } else {
                                $eve_outtime = '00:00';
                            }
                            echo $eve_intime.'-'.$eve_outtime;
                            ?>
                            <br /><span class="text-muted">E-Device:@if($sdevice_name){{ $sdevice_name->name }} @endif</span>
                        </td>
                        @if($dailyAttendance->designation_id != 1)
                        <td style="width:15%;">
                        <?php
                            
                            $mrng_shift_time = App\Model\WorkShift::where('shift_name','A')->first();
                            $eve_shift_time = App\Model\WorkShift::where('shift_name','B')->first();
                            if ($dailyAttendance->mrng_in_time != '') {  
                                if(strtotime(date("h:i",strtotime($mrng_shift_time->late_count_time))) < strtotime(date("h:i",strtotime($dailyAttendance->mrng_in_time)))){
                                   
                                    $array1 = explode(':', date("h:i",strtotime($mrng_shift_time->late_count_time)));
                                    $array2 = explode(':', date("h:i",strtotime($dailyAttendance->mrng_in_time)));
                                
                                    $minutes1 = ($array1[0] * 60.0 + $array1[1]);
                                    $minutes2 = ($array2[0] * 60.0 + $array2[1]);
                                
                                    echo $diff = $minutes2 - $minutes1.' Minutes'; 
                                
                            } 
                        }
                        ?>
                        </td>

                        <td style="width:15%;">
                        <?php   
                            if ($dailyAttendance->eve_in_time != ''){
                                if(strtotime(date("h:i",strtotime($eve_shift_time->late_count_time))) < strtotime(date("h:i",strtotime($dailyAttendance->eve_in_time)))){
                                   
                                    $array3 = explode(':', date("h:i",strtotime($eve_shift_time->late_count_time)));
                                    $array4 = explode(':', date("h:i",strtotime($dailyAttendance->eve_in_time)));
                                
                                    $minutes3 = ($array1[0] * 60.0 + $array3[1]);
                                    $minutes4 = ($array2[0] * 60.0 + $array4[1]);
                                
                                    echo $diff = $minutes4 - $minutes3.' Min'; 
                                
                            }else{
                                echo "Early";
                            } 
                                 
                            }else{
                                echo "";
                            } 
                                 
                             
                            ?>
                        </td>
                        @else
                            <td style="width:15%;"></td> 
                            <td style="width:15%;"></td> 
                        @endif
                    </tr>
                    @endif
                    @else
                    @php 

                    $fdevice_name = App\Model\Device::where('id',$dailyAttendance->first_device)->first();
                    $sdevice_name = App\Model\Device::where('id',$dailyAttendance->second_device)->first();
                    @endphp
                    <tr class="{!! $dailyAttendance->employee_id !!}">
                        <td style="width:5%;">{{ ++$dailyAttendanceSl }}</td>
                        <td style="width:15%;">
                            @if ($dailyAttendance->photo != '')
                            <img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/' . $dailyAttendance->photo) !!}" alt="user-img" class="img-circle">
                            @else
                            <img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">
                            @endif
                        </td>
                        <td style="width:20%;">
                            <span>{{ $dailyAttendance->fullName }}</span>
                            <br /><span class="text-muted">{{ $dailyAttendance->branch_name  }}</span>
                            <br /><span class="text-muted">ID:{{ $dailyAttendance->finger_id }}</span> 
                        </td>
                         
                        <td style="width:15%;"> 
                            <?php
                            if ($dailyAttendance->mrng_in_time != '') {
                                $mrng_intime= date("h:ia", strtotime($dailyAttendance->mrng_in_time));
                            } else {
                                $mrng_intime= '00:00';
                            }
                             
                            if ($dailyAttendance->mrng_out_time != '') {
                                $mrng_outtime= date("h:ia", strtotime($dailyAttendance->mrng_out_time));
                            } else {
                                $mrng_outtime='00:00';
                            }
                            echo $mrng_intime.'-'.$mrng_outtime;
                            ?>
                            <br /><span class="text-muted">M-Device: @if($fdevice_name){{ $fdevice_name->name }} @endif</span>
                        </td>
                        <td style="width:15%;"> 
                            <?php
                            if ($dailyAttendance->eve_in_time != '') {
                                $eve_intime = date("h:ia", strtotime($dailyAttendance->eve_in_time));
                            } else {
                                $eve_intime = '00:00';
                            }
                             
                            if ($dailyAttendance->eve_out_time != '') {
                                $eve_outtime = date("h:ia", strtotime($dailyAttendance->eve_out_time));
                            } else {
                                $eve_outtime = '00:00';
                            }
                            echo $eve_intime.'-'.$eve_outtime;
                            ?>
                             <br /><span class="text-muted">E-Device: @if($sdevice_name){{ $sdevice_name->name }} @endif</span>
                        </td>
                        @if($dailyAttendance->designation_id != 1)
                        <td style="width:15%;">                         
                        <?php
                            
                            $mrng_shift_time = App\Model\WorkShift::where('shift_name','A')->first();
                            $eve_shift_time = App\Model\WorkShift::where('shift_name','B')->first();
                            if ($dailyAttendance->mrng_in_time != '') {  
                                if(strtotime(date("h:i",strtotime($mrng_shift_time->late_count_time))) < strtotime(date("h:i",strtotime($dailyAttendance->mrng_in_time)))){
                                   
                                    $array1 = explode(':', date("h:i",strtotime($mrng_shift_time->late_count_time)));
                                    $array2 = explode(':', date("h:i",strtotime($dailyAttendance->mrng_in_time)));
                                
                                    $minutes1 = ($array1[0] * 60.0 + $array1[1]);
                                    $minutes2 = ($array2[0] * 60.0 + $array2[1]);
                                
                                    echo $diff = $minutes2 - $minutes1.' Min'; 
                                
                            } 
                        }
                        ?>
                        
                        </td>

                        <td style="width:15%;"> 
                            <?php
                             
                            if ($dailyAttendance->eve_in_time != ''){
                                if(strtotime(date("h:i",strtotime($eve_shift_time->late_count_time))) < strtotime(date("h:i",strtotime($dailyAttendance->eve_in_time)))){
                                   
                                    $array3 = explode(':', date("h:i",strtotime($eve_shift_time->late_count_time)));
                                    $array4 = explode(':', date("h:i",strtotime($dailyAttendance->eve_in_time)));
                                
                                    $minutes3 = ($array1[0] * 60.0 + $array3[1]);
                                    $minutes4 = ($array2[0] * 60.0 + $array4[1]);
                                
                                    echo $diff = $minutes4 - $minutes3.' Minutes'; 
                                
                            } 
                                 
                            }
                                 
                             
                            ?>
                            @else
                            <td style="width:15%;"></td> 
                            <td style="width:15%;"></td> 
                            @endif
                        </td> 
                       
                    </tr>

                       
                    </tr>
                    @endif
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


@if(isset($_POST['branch_id'])) 


                     
    <div class="row">
        <!-- Leave Request -->

    @if (count($leaveApplication) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.recent_leave_application')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($leaveApplication as $leaveApplication)
                            <div class="comment-center p-t-10 {{ $leaveApplication->leave_application_id }}">
                                <div class="comment-body">
                                    @if ($leaveApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $leaveApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($leaveApplication->created_at);
                                        @endphp
                                        <h5>{{ $leaveApplication->employee->first_name }}
                                            {{ $leaveApplication->employee->last_name }}</h5><span
                                            class="time">{{ date('d M Y h:i: a', $d) }}</span>
                                        <span class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                            @lang('leave.leave_type') :
                                            {{ $leaveApplication->leaveType->leave_type_name }}<br>
                                            @lang('leave.request_duration') :
                                            {{ dateConvertDBtoForm($leaveApplication->application_from_date) }}
                                            To
                                            {{ dateConvertDBtoForm($leaveApplication->application_to_date) }}<br>
                                            @lang('leave.number_of_day') : {{ $leaveApplication->number_of_day }}
                                            <br>
                                            @lang('leave.purpose') : {{ $leaveApplication->purpose }}
                                        </span>

                                        <a href="javacript:void(0)" data-status=2
                                            data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                            class="btn remarksForLeave btn btn-rounded btn-success btn-outline m-r-5"><i
                                                class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                        <a href="javacript:void(0)" data-status=3
                                            data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                            class="btn-rounded remarksForLeave btn btn-danger btn-outline"><i
                                                class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Permission Request -->
        @if (count($permissionApplication) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.recent_permission_application')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($permissionApplication as $permissionApplication)
                            <div class="comment-center p-t-10 {{ $permissionApplication->leave_permission_id }}">
                                <div class="comment-body">
                                    @if ($permissionApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $permissionApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($permissionApplication->created_at);
                                        @endphp
                                        <h5>{{ $permissionApplication->employee->first_name }}
                                            {{ $permissionApplication->employee->last_name }}</h5><span
                                            class="time">{{ date('d M Y h:i: a', $d) }}</span>
                                        <span class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                             
                                            @lang('leave.request_duration') :
                                            {{ dateConvertDBtoForm($permissionApplication->application_from_date) }}
                                            To
                                            {{ dateConvertDBtoForm($permissionApplication->application_to_date) }}<br>
                                            @lang('leave.number_of_day') : {{ $permissionApplication->number_of_day }}
                                            <br>
                                            @lang('leave.purpose') : {{ $permissionApplication->leave_permission_purpose }}
                                        </span>

                                        <a href="javacript:void(0)" data-status=2
                                            data-leave_permission_id="{{ $permissionApplication->leave_permission_id  }}"
                                            class="btn remarksForPermission btn btn-rounded btn-success btn-outline m-r-5"><i
                                                class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                        <a href="javacript:void(0)" data-status=3
                                            data-leave_permission_id ="{{ $permissionApplication->leave_permission_id  }}"
                                            class="btn-rounded remarksForPermission btn btn-danger btn-outline"><i
                                                class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif


    </div> 
    
    {!! Form::close() !!}
    @else
    <div class="row">
        <!-- Leave Request -->

    @if (count($leaveApplication) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.recent_leave_application')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($leaveApplication as $leaveApplication)
                            <div class="comment-center p-t-10 {{ $leaveApplication->leave_application_id }}">
                                <div class="comment-body">
                                    @if ($leaveApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $leaveApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($leaveApplication->created_at);
                                        @endphp
                                        <h5>{{ $leaveApplication->employee->first_name }}
                                            {{ $leaveApplication->employee->last_name }}</h5><span
                                            class="time">{{ date('d M Y h:i: a', $d) }}</span>
                                        <span class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                            @lang('leave.leave_type') :
                                            {{ $leaveApplication->leaveType->leave_type_name }}<br>
                                            @lang('leave.request_duration') :
                                            {{ dateConvertDBtoForm($leaveApplication->application_from_date) }}
                                            To
                                            {{ dateConvertDBtoForm($leaveApplication->application_to_date) }}<br>
                                            @lang('leave.number_of_day') : {{ $leaveApplication->number_of_day }}
                                            <br>
                                            @lang('leave.purpose') : {{ $leaveApplication->purpose }}
                                        </span>

                                        <a href="javacript:void(0)" data-status=2
                                            data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                            class="btn remarksForLeave btn btn-rounded btn-success btn-outline m-r-5"><i
                                                class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                        <a href="javacript:void(0)" data-status=3
                                            data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                            class="btn-rounded remarksForLeave btn btn-danger btn-outline"><i
                                                class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Permission Request -->
        @if (count($permissionApplication) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.recent_permission_application')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($permissionApplication as $permissionApplication)
                            <div class="comment-center p-t-10 {{ $permissionApplication->leave_permission_id }}">
                                <div class="comment-body">
                                    @if ($permissionApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $permissionApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($permissionApplication->created_at);
                                        @endphp
                                        <h5>{{ $permissionApplication->employee->first_name }}
                                            {{ $permissionApplication->employee->last_name }}</h5><span
                                            class="time">{{ date('d M Y h:i: a', $d) }}</span>
                                        <span class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                             
                                            @lang('leave.request_duration') :
                                            {{ dateConvertDBtoForm($permissionApplication->application_from_date) }}
                                            To
                                            {{ dateConvertDBtoForm($permissionApplication->application_to_date) }}<br>
                                            @lang('leave.number_of_day') : {{ $permissionApplication->number_of_day }}
                                            <br>
                                            @lang('leave.purpose') : {{ $permissionApplication->purpose }}
                                        </span>

                                        <a href="javacript:void(0)" data-status=2
                                            data-leave_permission_id="{{ $permissionApplication->leave_permission_id  }}"
                                            class="btn remarksForPermission btn btn-rounded btn-success btn-outline m-r-5"><i
                                                class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                        <a href="javacript:void(0)" data-status=3
                                            data-leave_permission_id ="{{ $permissionApplication->leave_permission_id  }}"
                                            class="btn-rounded remarksForPermission btn btn-danger btn-outline"><i
                                                class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif


    </div> 
    @endif
    

</div>
 

@endsection
<?php
function expire($doc, $date)
{
    return ['doc' => $doc, 'date' => DATE('d-m-Y', strtotime($date)), 'days' => days($date, DATE('Y-m-d'))];
}

function days($from_date, $to_date)
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);
    $days = $date2->diff($date1)->format('%a');
    return $days;
}
?>

@section('page_scripts')
<link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>
<script type="text/javascript">
    (function() {

        $(".demo1").bootstrapNews({
            newsPerPage: 2,
            autoplay: true,
            pauseOnHover: true,
            direction: 'up',
            newsTickerInterval: 4000,
            onToDo: function() {
            }
        });

    })();

    // $(".branch_id").change(function (e) {
    //     var branchId = $(this).val();

    //     var actionTo = "{{ URL::to('getDashboardBranchData') }}";
    //             var token = '{{ csrf_token() }}';
             
    //                 $.ajax({
    //                     type: 'POST',
    //                     url: actionTo,
    //                     data: {
    //                         branchId: branchId, 
    //                         _token: token
    //                     },
                      

    //                 }); 



    // });
//     $(document).on('change', '.remarksForLeave', function() {
// #branchEmployees

//     });
    $(document).on('click', '.branch_id', function() {

        var actionTo = "{{ URL::to('approveOrRejectLeaveApplication') }}";
        var leave_application_id = $(this).attr('data-leave_application_id');
        var status = $(this).attr('data-status');

        if (status == 2) {
            var statusText = "Are you want to approve leave application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject leave application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            leave_application_id: leave_application_id,
                            status: status,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "Leave application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "Leave application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                        }
                                    });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;

    });

    document.getElementById('absentDetail').addEventListener('click', function() {
        document.getElementById('show_details').classList.toggle('hidden');
    });
    /* 
        if ($('.pagination').find('li.active span').html() != 1) {
            $('#absentDetail').trigger('click');
        } */
        
$(document).on('click', '.remarksForLeave', function() {

var actionTo = "{{ URL::to('approveOrRejectLeaveApplication') }}";
var leave_application_id = $(this).attr('data-leave_application_id');
var status = $(this).attr('data-status');

if (status == 2) {
    var statusText = "Are you want to approve leave application?";
    var btnColor = "#2cabe3";
} else {
    var statusText = "Are you want to reject leave application?";
    var btnColor = "red";
}

swal({
        title: "",
        text: statusText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: btnColor,
        confirmButtonText: "Yes",
        closeOnConfirm: false
    },
    function(isConfirm) {
        var token = '{{ csrf_token() }}';
        if (isConfirm) {
            $.ajax({
                type: 'POST',
                url: actionTo,
                data: {
                    leave_application_id: leave_application_id,
                    status: status,
                    _token: token
                },
                success: function(data) {
                    if (data == 'approve') {
                        swal({
                                title: "Approved!",
                                text: "Leave application approved.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_application_id).fadeOut();
                                }
                            });

                    } else {
                        swal({
                                title: "Rejected!",
                                text: "Leave application rejected.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_application_id).fadeOut();
                                }
                            });
                    }
                }

            });
        } else {
            swal("Cancelled", "Your data is safe .", "error");
        }
    });
return false;

});

$(document).on('click', '.remarksForPermission', function() {

var actionTo = "{{ URL::to('approveOrRejectPermissionApplication') }}";
var leave_permission_id = $(this).attr('data-leave_permission_id');
var status = $(this).attr('data-status');

if (status == 2) {
    var statusText = "Are you want to approve permission application?";
    var btnColor = "#2cabe3";
} else {
    var statusText = "Are you want to reject permission application?";
    var btnColor = "red";
}

swal({
        title: "",
        text: statusText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: btnColor,
        confirmButtonText: "Yes",
        closeOnConfirm: false
    },
    function(isConfirm) {
        var token = '{{ csrf_token() }}';
        if (isConfirm) {
            $.ajax({
                type: 'POST',
                url: actionTo,
                data: {
                    leave_permission_id: leave_permission_id,
                    status: status,
                    _token: token
                },
                success: function(data) {
                    if (data == 'approve') {
                        swal({
                                title: "Approved!",
                                text: "Permissiom application approved.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_permission_id).fadeOut();
                                }
                            });

                    } else {
                        swal({
                                title: "Rejected!",
                                text: "Permission application rejected.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_permission_id).fadeOut();
                                }
                            });
                    }
                }

            });
        } else {
            swal("Cancelled", "Your data is safe .", "error");
        }
    });
return false;

});

</script>
@endsection