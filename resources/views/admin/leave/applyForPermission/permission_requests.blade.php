@extends('admin.master')
@section('content')
@section('title')
@lang('leave.permission_requests')
@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	
			 	
		</div>
					
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(session()->has('success'))
								<div class="alert alert-success alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
								</div>
							@endif
							@if(session()->has('error'))
								<div class="alert alert-danger alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif
							@if (count($permissionresults) > 0)
							<div class="">
								<table class="table table-hover manage-u-table">
									<thead >
                                         <tr>
                                            <th>#</th>
                                            <th>@lang('common.employee_name')</th> 
                                            <th>@lang('leave.request_duration')</th>
                                            <th>@lang('leave.date')</th>
											<th>@lang('leave.already_applied_permission_count')</th>
                                            <th>@lang('leave.department_head_status')</th>                                        
                                        </tr>
									</thead>
									<tbody>
										{!! $sl=null !!}									 

        						
										@foreach($permissionresults AS $value)
											<tr>
												<td style="width: 100px;">{!! ++$sl !!}</td>
												<td>
													@if(isset($value->employee->first_name)) {!! $value->employee->first_name !!} @endif
													@if(isset($value->employee->last_name)) {!! $value->employee->last_name !!} @endif
												</td>

												<td>{!! $value->permission_duration !!}</td>

												<td> {!! date('d-m-Y',strtotime($value->leave_permission_date)) !!}   </td>
												 
												<td>
                                                    @php
													$Year  = date("Y",strtotime($value->leave_permission_date));
													$Month = (int)date("m",strtotime($value->leave_permission_date));
													$checkpermissions = App\Model\LeavePermission::whereMonth('leave_permission_date','=',$Month)->whereYear('leave_permission_date','=',$Year)
													->where('employee_id',$value->employee_id)->where('status',1)->count();
                                                    @endphp

                                                    @if(isset($checkpermissions)) 
                                                        {{ $checkpermissions }}
												    @else 
                                                        {{ '0' }}
												    @endif
                                                
												</td>
												<td  style="width: 100px;"> 
												<a href="javacript:void(0)" data-status=1
                                            data-leave_application_id="{{ $value->leave_permission_id  }}"
                                            class="btn remarksForDepartmentHead btn btn-rounded btn-success btn-outline m-r-5"><i
                                                class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                        <a href="javacript:void(0)" data-status=2
                                            data-leave_application_id="{{ $value->leave_permission_id  }}"
                                            class="btn-rounded remarksForDepartmentHead btn btn-danger btn-outline"><i
                                                class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
												</td>										 
												 
											</tr>
										@endforeach
										
									</tbody>
								</table>
								<div class="text-center">
									{{$permissionresults->links()}}
								</div>
							</div>
							@endif
							 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('page_scripts')
<link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>

    <script type="text/javascript"> 

   /* Leave Permission ->Department Head Approval */
   $(document).on('click', '.remarksForDepartmentHead', function() {

var actionTo = "{{ URL::to('approveOrRejectLeavePermissionByDepartmentHead') }}";
var leave_application_id = $(this).attr('data-leave_application_id');
var status = $(this).attr('data-status');

if (status == 1) {
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
                    leave_application_id: leave_application_id,
                    status: status,
                    _token: token
                },
                success: function(data) {
                    if (data == 'approve') {
                        swal({
                                title: "Approved!",
                                text: "Leave permission approved.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_application_id).fadeOut();
                                    location.reload();
                                }
                            });

                    } else {
                        swal({
                                title: "Rejected!",
                                text: "Leave permission rejected.",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('.' + leave_application_id).fadeOut();
                                    location.reload();
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

/* Leave Permission ->Plant Head Approval */
$(document).on('click', '.remarksForPlantHead', function() {

var actionTo = "{{ URL::to('approveOrRejectLeavePermissionByPlantHead') }}";
var leave_application_id = $(this).attr('data-leave_application_id');
var status = $(this).attr('data-status');

if (status == 1) {
var statusText = "Are you want to approve leave permission?";
var btnColor = "#2cabe3";
} else {
var statusText = "Are you want to reject leave permission?";
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
                                location.reload();
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
                                location.reload();
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
