@extends('admin.master')
@section('content')
@php
	function days($from_date,$to_date){
        $date1 = new \DateTime($from_date);
        $date2 = new \DateTime($to_date);
        $days  = $date2->diff($date1)->format('%a');
        return $days;
    }
@endphp
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
		   <ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>Office Management Details</li>
			</ol>
		</div>	
		<div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
			<a href="{{ route('reminder.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Add office Management</a>
		</div>	
	</div>
                
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Office Management Details</div>
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
						<div class="table-responsive">
							<table id="myTable" class="table table-bordered">
								<thead>
									 <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Title</th>
                                        <th>Expiry Date</th>
                                        <th>Expire Day(s)</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($results AS $value)
										<tr class="{!! $value->reminder_id !!}">
											<td style="width: 100px;">{!! ++$sl !!}</td>
											<td style="width: 100px;">{!! $value->title !!}</td>
											<td>{{ ($value->expiry_date != "0000-00-00") ? DATE('d-m-Y',strtotime($value->expiry_date)) : '' }}</td>
											<td>{{ days(DATE('Y-m-d'),$value->expiry_date ) }}</td>
											<td style="width: 100px;">
												<a href="{!! route('reminder.edit',$value->reminder_id) !!}"  class="btn btn-success btn-xs btnColor">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>
												<a href="{!!route('reminder.delete',$value->reminder_id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->reminder_id!!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
											</td>
										</tr>
									@endforeach
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
