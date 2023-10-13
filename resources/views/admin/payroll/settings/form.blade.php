@extends('admin.master')
@section('content')
@section('title')
@if(isset($editModeData))
@lang('salary.edit_settings')
@else
@lang('salary.add_settings')
@endif
@endsection

<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>

			</ol>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						@if(isset($editModeData))
						{{ Form::model($editModeData, array('route' => array('payroll.settingsstore', $editModeData->department_id), 'method' => 'PUT','files' => 'true','class' => 'form-horizontal')) }}
						@else
						{{ Form::open(array('route' => 'payroll.settingsstore','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
						@endif
						<div class="form-body">
							<div class="row">
								<div class="col-md-offset-2 col-md-6">
									@if($errors->any())
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
										@foreach($errors->all() as $error)
										<strong>{!! $error !!}</strong><br>
										@endforeach
									</div>
									@endif
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
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('salary.basic')<span class="validateRq">*</span></label>
										<div class="col-md-6">
											{!! Form::text('basic',Input::old('basic') ? Input::old('basic') : $settings->basic, $attributes = array('class'=>'form-control required basic','id'=>'basic','placeholder'=>__('salary.basic'))) !!}
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-4">@lang('salary.hra')<span class="validateRq">*</span></label>
										<div class="col-md-6">
											{!! Form::text('hra',Input::old('hra') ? Input::old('hra') : $settings->hra, $attributes = array('class'=>'form-control required hra','id'=>'hra','placeholder'=>__('salary.hra'))) !!}
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-4">@lang('salary.employee_esic')<span class="validateRq">*</span></label>
										<div class="col-md-6">
											{!! Form::text('employee_esic',Input::old('employee_esic') ? Input::old('employee_esic') : $settings->employee_esic, $attributes = array('class'=>'form-control required employee_esic','id'=>'employee_esic','placeholder'=>__('salary.employee_esic'))) !!}
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-4">@lang('salary.employee_pf')<span class="validateRq">*</span></label>
										<div class="col-md-6">
											{!! Form::text('employee_pf',Input::old('employee_pf') ? Input::old('employee_pf') : $settings->employee_pf, $attributes = array('class'=>'form-control required employee_pf','id'=>'employee_pf','placeholder'=>__('salary.employee_pf'))) !!}
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-4">Working Days<span class="validateRq">*</span></label>
										<div class="col-md-6">
											@php 
											if($settings->working_days==0)
												$days="";
											else
											 	$days=$settings->working_days;
											@endphp
											{!! Form::text('working_days',Input::old('working_days') ? Input::old('working_days') : $days, $attributes = array('class'=>'form-control required working_days','id'=>'working_days')) !!}
										<small>If leave this field as blank, let consider corresponding months days count (28,30,31) </small>
										</div>
									</div>
									<div class="form-group" style="display:none;">
										<label class="control-label col-md-4">Per Day Working Hours<span class="validateRq">*</span></label>
										<div class="col-md-6">
											{!! Form::text('wotking_hours',Input::old('wotking_hours') ? Input::old('working_hours') : $settings->working_hours, $attributes = array('class'=>'form-control required working_hours','id'=>'working_hours','value'=>8,'placeholder'=>__('salary.working_hours'))) !!}
							
										</div>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<div class="row">
									<div class="col-md-8">
										<div class="row">
											<div class="col-md-offset-4 col-md-8">
												@if(isset($editModeData))
												<button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.update')</button>
												@else
												<button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection