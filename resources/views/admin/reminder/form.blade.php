@extends('admin.master')
@section('content')

<?php
if(isset($editModeData))
	$title="Edit Office Management";
else
	$title="Add Office Management";
?>

	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-4 col-md-5 col-sm-4 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>{{ $title }}</li>
				  
				</ol>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<a href="{{route('reminder.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> Office Management Details</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>{{ $title }}</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(isset($editModeData))
								{{ Form::model($editModeData, array('route' => array('reminder.update', $editModeData->reminder_id), 'method' => 'PUT','files' => 'true','id' => 'reminderForm','class' => 'form-horizontal')) }}
							@else
								{{ Form::open(array('route' => 'reminder.store','enctype'=>'multipart/form-data','id'=>'reminderForm','class' => 'form-horizontal')) }}
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
												<label class="control-label col-md-4">Title<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('title',Input::old('title'), $attributes = array('class'=>'form-control required title','id'=>'title')) !!}
												</div>
											</div>
										</div>
									</div>	
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Expiry Date<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::text('expiry_date',Input::old('expiry_date'), $attributes = array('class'=>'form-control c-dateField required expiry_date','id'=>'expiry_date','autocomplete'=>'off')) !!}
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Content <span class="validateRq">*</span></label>
												<div class="col-md-8">
													<textarea class="form-control" rows="6" name="content">@if(old('content')) {{ old('content') }}@elseif(isset($editModeData)) {{ $editModeData->content }}@endif</textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Upload Document<span class="validateRq">*</span></label>
												<div class="col-md-8">
													{!! Form::file('file',Input::old('file'), $attributes = array('class'=>'form-control required file','id'=>'file')) !!}
													<br><p><small><i>Allowed file extensions : jpeg,jpg,png,pdf</i></small></p>
													<div>
														<hr>
													<h4>Document</h4>
													@if(isset($editModeData))
														<?php 

															$filename='../../uploads/officeManagement/'.$editModeData->file;
															$extension=\File::extension($filename);
																											
														if($extension=="pdf")
															echo '<a href="'.route("reminder.documentdownload",["file"=>$editModeData->file]).'"><img src="../../uploads/front/pdf-placeholder.png" style="width: 150px;height: 150px;border: 1px solid grey;border-radius: .4em;cursor: pointer;" title="Click to Download">
															</a>';
														else
															echo '<a href="'.route('reminder.documentdownload',['file'=>$editModeData->file]).'">
															<img src="../../uploads/officeManagement/'.$editModeData->file.'" style="width: 150px;height: 150px;border: 1px solid grey;border-radius: .4em;cursor: pointer;" title="Click to Download">
															</a>';
														?>
													@endif
												</div>
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
@section('page_scripts')
<script type="text/javascript">
	 $(document).on("focus", ".c-dateField", function() {
            $(this).datepicker({
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                clearBtn: true
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });
</script>

@endsection

