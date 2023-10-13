@extends('admin.master')
@section('content')
@section('title')
@lang('employee.employee_photo_upload')
@endsection
<section class="content">
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
							@lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>
		</div>
		<div class="row">

			<div class="col-md-12">

				<div class="panel panel-info">

					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>

					<div class="panel-wrapper collapse in" aria-expanded="true">

						<div class="panel-body">

							@if ($errors->any())
							<div class="alert alert-danger alert-dismissible" role="alert">

								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ã—</span></button>

								@foreach ($errors->all() as $error)
								<strong>{!! $error !!}</strong><br>
								@endforeach

							</div>
							@endif

							@if (session()->has('success'))
							<div class="alert alert-success alert-dismissable">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">Ã—</button>

								<i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>

							</div>
							@endif

							@if (session()->has('error'))
							<div class="alert alert-danger alert-dismissable">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">Ã—</button>

								<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>

							</div>
							@endif

							{{ Form::open(['route' => 'photo.importstore', 'enctype' => 'multipart/form-data', ])}}

							<div class="box-body">
								<p style="font-size:12px;font-style:italic;color:red;"><b>Note : Photo Name must be Finger Id.</b> || <b>Size</b> : Max 1 MB | <b>File Type</b> : jpg & jpeg | <b>Dimensions</b> - Max Width : 500px , Max Height : 600px | <b>Max</b> : 10 Photos </p>
								<div class="row">
									<div class="col-md-offset-2 col-md-6">
										@if ($errors->any())
										<div class="alert alert-danger alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
											@foreach ($errors->all() as $error)
											<strong>{!! $error !!}</strong><br>
											@endforeach
										</div>
										@endif
										@if (session()->has('success'))
										<div class="alert alert-success alert-dismissable">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
										</div>
										@endif
										@if (session()->has('error'))
										<div class="alert alert-danger alert-dismissable">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
										</div>
										@endif
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-md-offset-3">
										<div class="form-group row">
											<label for="photo" class="col-sm-4 control-label required">Upload Photos</label>
											<div class="col-sm-8">
												<input type="file" class="col-sm-4 form-control" name="photo[]" multiple>


											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<div class="text-center">
									<input type="submit" name="" class="btn btn-info">
									<a href="{{route('photo.import')}}" class="btn btn-default">Cancel</a>
								</div>
							</div>
							{{ Form::close() }}
							<div class="table-responsive">

								<table id="myTable" class="table table-hover manage-u-table">

									<thead class="bg-title">
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Photo</th>
											<th>Created At</th>

										</tr>

									</thead>
									<tbody>
										@php
										$photos = App\Model\Photos::where('status', 1)->orderBy('photo_id', 'DESC')->get();
										@endphp
										@foreach ($photos as $key => $Data)
										<tr>
											<td>{{ $key+1}}</td>
											<td>{{ $Data->name }} </td>
											<td>

												@if ($Data->name != '')

												<img style=" width: 70px; " src="{!! asset('uploads/photo/' . $Data->name) !!}" alt="user-img" class="img-circle">

												@else

												<img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">

												@endif

											</td>
											<td>{{ $Data->created_at }} </td>
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
	</div>
	<hr>
	</div>
</section>
@endsection('content')