@extends('admin.master')
@section('content')
@section('title')
@lang('employee.cron_status_report')
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
						@lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>
			
	</div>
	<div class="row"> 
		<div class="col-sm-12">
		<table id="myTable" class="table table-bordered table-striped ">
        <thead>
            <tr class="tr_header">
			    <th  style="width:20%">SNo</th>     
                <th style="width:55%"> Requested Date</th>
				<th  style="width:25%">Status</th>            
            </tr>
        </thead>
        <tbody>            
		@php
		$s=1;
			foreach ($crons as $cron){     			 
		@endphp	              

				<tr class="{!! $cron->cron_id !!}">
                    <td>{!!  $s++ !!}</td> 
                    <td>{!! DATE('d-m-Y h:i a',strtotime($cron->created_at))  !!} </td>
					<td>@if($cron->status == 0 ){!! 'Pending' !!}
						@elseif($cron->status == 1 ){!! 'Running' !!}
						@elseif($cron->status == 2 ){!! 'Completed' !!}
						@endif
					</td>
                </tr>
			@php
            }
			@endphp
        </tbody>
    </table>
		</div>
	</div>
</div>
@endsection
@section('page_scripts') 
@endsection

 