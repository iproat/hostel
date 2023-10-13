@extends('admin.master')
@section('content')

<?php
	$title="Mail Settings";
?>

	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-4 col-md-5 col-sm-4 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>{{ $title }}</li>
				  
				</ol>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>{{ $title }}</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							
								{{ Form::model($editModeData, array('route' => array('reminder.settings.store'),'files' => 'true','id' => 'reminderForm','class' => 'form-horizontal')) }}
							
							
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
												<label class="control-label col-md-4">Admin Email ID's<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('email_ids',Input::old('email_ids'), $attributes = array('class'=>'form-control required email_ids','id'=>'email_ids')) !!}
													<p><small>Multiple Email ID's should be comma ( ,) seperated</small> </p>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Employee Document Expiry Mail Subject<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('employeedoc_mail_subject',Input::old('employeedoc_mail_subject'), $attributes = array('class'=>'form-control required employeedoc_mail_subject','id'=>'employeedoc_mail_subject')) !!}
													
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Employee Document Expiry Admin Mail Subject<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('employeedoc_mail_admin_subject',Input::old('employeedoc_mail_admin_subject'), $attributes = array('class'=>'form-control required employeedoc_mail_admin_subject','id'=>'employeedoc_mail_admin_subject')) !!}
													
												</div>
											</div>
										</div>
									</div>	
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Employee Document Expiry Mail Sender Name<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('employeedoc_sender_name',Input::old('employeedoc_sender_name'), $attributes = array('class'=>'form-control required employeedoc_sender_name','id'=>'employeedoc_sender_name')) !!}
													
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Office Document Expiry Mail Subject<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('officedoc_mail_subject',Input::old('officedoc_mail_subject'), $attributes = array('class'=>'form-control required officedoc_mail_subject','id'=>'officedoc_mail_subject')) !!}
													
												</div>
											</div>
										</div>
									</div>	
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Office Document Expiry Mail Sender Name<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('officedoc_sender_name',Input::old('officedoc_sender_name'), $attributes = array('class'=>'form-control required officedoc_sender_name','id'=>'officedoc_sender_name')) !!}
													
												</div>
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
							{{ Form::close() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


