@extends('admin.master')

@section('content')

@section('title')
    @lang('employee.edit_employee')
@endsection

<style>
    .appendBtnColor {

        color: #fff;

        font-weight: 700;

    }
</style>



<div class="container-fluid">

    <div class="row bg-title">

        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">

            <ol class="breadcrumb">

                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>

                <li>@yield('title')</li>



            </ol>

        </div>


        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">

            <a href="{{ route('employee.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('employee.view_employee')</a>

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

                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">�</span></button>

                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach

                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">

                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>

                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>

                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">

                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>

                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>

                            </div>
                        @endif

                        {{ Form::model($editModeData, [
                            'route' => ['employee.update', $editModeData->employee_id],
                            'method' => 'PUT',
                            'files' => 'true',
                            'id' => 'employeeForm',
                        ]) }}

                        <input class="form-control  delete_education_qualifications_cid"
                            id="delete_education_qualifications_cid" name="delete_education_qualifications_cid"
                            type="hidden" value="">

                        <input class="form-control  delete_experiences_cid" id="delete_experiences_cid"
                            name="delete_experiences_cid" type="hidden" value="">

                        <div class="form-body">

                            <h3 class="box-title">@lang('employee.employee_account')</h3>

                            <hr>

                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.role')<span
                                                class="validateRq">*</span></label>

                                        <select name="role_id" class="form-control user_id required select2" required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($roleList as $value)
                                                <option value="{{ $value->role_id }}"
                                                    @if ($value->role_id == $employeeAccountEditModeData->role_id) {{ 'selected' }} @endif>
                                                    {{ $value->role_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.user_name')<span class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <div class="input-group-addon"><i class="ti-user"></i></div>

                                        <input class="form-control required user_name" required id="user_name"
                                            placeholder="@lang('employee.user_name')" name="user_name" type="text"
                                            value="{{ $employeeAccountEditModeData->user_name }}">

                                    </div>

                                </div>

                            </div>

                            <h3 class="box-title">@lang('employee.personal_information')</h3>

                            <hr>

                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.first_name')<span
                                                class="validateRq">*</span></label>

                                        <input class="form-control required first_name" id="first_name"
                                            placeholder="@lang('employee.first_name')" name="first_name" type="text"
                                            value="{{ $editModeData->first_name }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.last_name')</label>

                                        <input class="form-control last_name" id="last_name"
                                            placeholder="@lang('employee.last_name')" name="last_name" type="text"
                                            value="{{ $editModeData->last_name }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.finger_print_no')<span
                                                class="validateRq">*</span></label>

                                        <input class="form-control number finger_id" id="finger_id"
                                            placeholder="@lang('employee.finger_print_no')" name="finger_id" type="text"
                                            value="{{ $editModeData->finger_id }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.supervisor')</label>

                                        <select name="supervisor_id"
                                            class="form-control supervisor_id required select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($supervisorList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if ($value->employee_id == $editModeData->supervisor_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name }} {{ $value->last_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                            </div>



                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('department.department_name')<span
                                                class="validateRq">*</span></label>

                                        <select name="department_id" class="form-control department_id  select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if ($value->department_id == $editModeData->department_id) {{ 'selected' }} @endif>
                                                    {{ $value->department_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('designation.designation_name')<span
                                                class="validateRq">*</span></label>

                                        <select name="designation_id" class="form-control department_id select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($designationList as $value)
                                                <option value="{{ $value->designation_id }}"
                                                    @if ($value->designation_id == $editModeData->designation_id) {{ 'selected' }} @endif>
                                                    {{ $value->designation_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('branch.branch_name')</label>

                                        <select name="branch_id" class="form-control branch_id select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($branchList as $value)
                                                <option value="{{ $value->branch_id }}"
                                                    @if ($value->branch_id == $editModeData->branch_id) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>
                                 <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.blood_group')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-blood_group"></i></span>

                                        <input class="form-control text blood_group" id="blood_group"
                                            placeholder="@lang('employee.blood_group')" name="blood_group" type="text"
                                            value="{{ $editModeData->blood_group }}">

                                    </div>

                                </div>
{{-- 
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('work_shift.work_shift_name')<span
                                                class="validateRq">*</span></label>

                                        <select name="work_shift_id" class="form-control work_shift_id select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($workShiftList as $value)
                                                <option value="{{ $value->work_shift_id }}"
                                                    @if ($value->work_shift_id == $editModeData->work_shift_id) {{ 'selected' }} @endif>
                                                    {{ $value->shift_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div> --}}

                            </div>



                            <div class="row">

                                {{-- <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.phone')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input class="form-control number phone" id="phone"
                                            placeholder="@lang('employee.phone')" name="phone" type="number"
                                            value="{{ $editModeData->phone }}">

                                    </div>

                                </div> --}}


                                {{-- <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.personal_email')</label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                                        <input class="form-control text personal_email" id="personal_email"
                                            placeholder="@lang('employee.personal_email')" name="personal_email" type="text"
                                            value="{{ $editModeData->personal_email }}">


                                    </div>

                                </div> --}}
                                {{-- <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.official_email')</label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                                        <input class="form-control text official_email" id="official_email"
                                            placeholder="@lang('employee.official_email')" name="official_email" type="text"
                                            value="{{ $editModeData->official_email }}">


                                    </div>

                                </div> --}}
                                {{-- <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.blood_group')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-blood_group"></i></span>

                                        <input class="form-control text blood_group" id="blood_group"
                                            placeholder="@lang('employee.blood_group')" name="blood_group" type="text"
                                            value="{{ $editModeData->blood_group }}">

                                    </div>

                                </div> --}}

                            </div>



                        </div>



                        <div class="row">

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.gender')<span
                                            class="validateRq">*</span></label>

                                    <select name="gender" class="form-control gender select2">

                                        <option value="">--- @lang('common.please_select') ---</option>

                                        <option value="Male"
                                            @if ('Male' == $editModeData->gender) {{ 'selected' }} @endif>
                                            @lang('employee.male')</option>

                                        <option value="Female"
                                            @if ('Female' == $editModeData->gender) {{ 'selected' }} @endif>
                                            @lang('employee.female')</option>

                                    </select>

                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">Status<span class="validateRq">*</span></label>

                                    <select name="status" class="form-control status select2">

                                        <option value="1"
                                            @if ('1' == $editModeData->status) {{ 'selected' }} @endif>
                                            @lang('common.active')</option>

                                        <option value="2"
                                            @if ('2' == $editModeData->status) {{ 'selected' }} @endif>
                                            @lang('common.inactive')</option>

                                        <option value="3"
                                            @if ('3' == $editModeData->status) {{ 'selected' }} @endif>
                                            @lang('common.terminated')</option>

                                    </select>

                                </div>

                            </div>
                            <div class="col-md-3">

                                <label for="exampleInput">@lang('employee.photo')</label>

                                <div class="input-group">

                                    <span class="input-group-addon"><i class="	fa fa-picture-o"></i></span>

                                    <input class="form-control photo" id="photo"
                                        accept="image/png, image/jpeg, image/gif,image/jpg" name="photo"
                                        type="file">

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.address')</label>

                                    <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                        rows="2" name="address">{{ $editModeData->address }}</textarea>

                                </div>

                            </div>

                            {{-- <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.faith')</label>

                                    <input class="form-control faith" id="faith" placeholder="@lang('employee.faith')"
                                        name="faith" type="text" value="{{ $editModeData->faith }}">

                                </div>

                            </div> --}}

                            {{-- <div class="col-md-3">

                                <label for="exampleInput">@lang('employee.date_of_birth')<span class="validateRq">*</span></label>

                                <div class="input-group">

                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                    <input class="form-control date_of_birth dateField" id="date_of_birth" readonly
                                        placeholder="@lang('employee.date_of_birth')" name="date_of_birth" type="text"
                                        value="{{ dateConvertDBtoForm($editModeData->date_of_birth) }}">

                                </div>

                            </div> --}}

                            {{-- <div class="col-md-3">

                                <label for="exampleInput">@lang('employee.date_of_joining')<span class="validateRq">*</span></label>

                                <div class="input-group">

                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                    <input class="form-control date_of_joining dateField" id="date_of_joining"
                                        readonly placeholder="@lang('employee.date_of_joining')" name="date_of_joining"
                                        type="text"
                                        value="{{ dateConvertDBtoForm($editModeData->date_of_joining) }}">

                                </div>

                            </div> --}}

                        </div>





                        <div class="row">

                            {{-- <div class="col-md-3">

                                <label for="exampleInput">@lang('employee.date_of_leaving')</label>

                                <div class="input-group">

                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                    <input class="form-control  date_of_leaving dateField" id="date_of_leaving"
                                        readonly placeholder="@lang('employee.date_of_leaving')" name="date_of_leaving"
                                        type="text"
                                        value="{{ dateConvertDBtoForm($editModeData->date_of_leaving) }}">

                                </div>

                            </div> --}}

                            {{-- <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.marital_status')</label>

                                    <select name="marital_status" class="form-control status required select2">

                                        <option value="">--- Please select ---</option>

                                        <option value="Unmarried"
                                            @if ('Unmarried' == $editModeData->marital_status) {{ 'selected' }} @endif>
                                            @lang('employee.unmarried')</option>

                                        <option value="Married"
                                            @if ('Married' == $editModeData->marital_status) {{ 'selected' }} @endif>
                                            @lang('employee.married')</option>

                                    </select>

                                </div>

                            </div> --}}






                        </div>

                        <div class="row">



                        </div>
                        <br>

                        <!-- <h3> Card Details</h3><hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">@lang('employee.pf_account_number')</label>
                                    <input class="form-control text pf_account_number" id="pf_account_number"
                                        placeholder="@lang('employee.pf_account_number')" name="pf_account_number" type="number"
                                        value="{{ $editModeData->pf_account_number }}">
                                </div>
                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.esi_card_number')</label>

                                    <input class="form-control text esi_card_number" id="esi_card_number"
                                        placeholder="@lang('employee.esi_card_number')" name="esi_card_number" type="number"
                                        value="{{ $editModeData->esi_card_number }}">

                                </div>
                            </div>
                             <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 1 Title</label>
                                        <input class="form-control card_title1" id="card_title1"
                                            placeholder="" name="card_title1" type="text"
                                            value="{{ $editModeData->card_title1 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 1 Number</label>

                                        <input class="form-control card_number1" id="card_number1"
                                            placeholder="" name="card_number1" type="number"
                                            value="{{ $editModeData->card_number1 }}">

                                    </div>

                                </div>
                        </div>
                         <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 2 Title</label>
                                        <input class="form-control card_title2" id="card_title2"
                                            placeholder="" name="card_title2" type="text"
                                            value="{{ $editModeData->card_title2 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 2 Number</label>

                                        <input class="form-control card_number2" id="card_number2"
                                            placeholder="" name="card_number2" type="number"
                                            value="{{ $editModeData->card_number2 }}">

                                    </div>

                                </div>

                                      <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 3 Title</label>
                                        <input class="form-control card_title3" id="card_title3"
                                            placeholder="" name="card_title3" type="text"
                                            value="{{ $editModeData->card_title3 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 3 Number</label>

                                        <input class="form-control card_number3" id="card_number3"
                                            placeholder="" name="card_number3" type="number"
                                            value="{{ $editModeData->card_number3 }}">

                                    </div>

                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 4 Title</label>
                                        <input class="form-control card_title4" id="card_title4"
                                            placeholder="" name="card_title4" type="text"
                                            value="{{ $editModeData->card_title4 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 4 Number</label>

                                        <input class="form-control card_number4" id="card_number4"
                                            placeholder="" name="card_number4" type="number"
                                            value="{{ $editModeData->card_number4 }}">

                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 5 Title</label>
                                        <input class="form-control card_title5" id="card_title5"
                                            placeholder="" name="card_title5" type="text"
                                            value="{{ $editModeData->card_title5 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 5 Number</label>

                                        <input class="form-control card_number5" id="card_number5"
                                            placeholder="" name="card_number5" type="number"
                                            value="{{ $editModeData->card_number5 }}">

                                    </div>

                                </div>

                            </div>
                         Working -->


                        <!--
                        <br> -->
                        <!-- <h3 class="box-title"> Emergency Contact Details</h3>

                        <hr>

                        <div class="row">

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.emergency_contact')</label>

                                    <input class="form-control text emergency_contact" id="emergency_contact"
                                        placeholder="@lang('employee.emergency_contact')" name="emergency_contact" type="text"
                                        value="{{ $editModeData->emergency_contact }}">


                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.contact_person_name')</label>

                                    <input class="form-control text contact_person_name" id="contact_person_name"
                                        placeholder="@lang('employee.contact_person_name')" name="contact_person_name" type="text"
                                        value="{{ $editModeData->contact_person_name }}">


                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.relation_of_contact_person')</label>

                                    <input class="form-control text relation_of_contact_person"
                                        id="relation_of_contact_person" placeholder="@lang('employee.relation_of_contact_person')"
                                        name="relation_of_contact_person" type="text"
                                        value="{{ $editModeData->relation_of_contact_person }}">

                                </div>

                            </div>



                        </div> -->
                        <!-- <h3> Document Details</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Aadhar Number</label>
                                    <input type="text" class="form-control" name="document_title"
                                        value="{{ $editModeData->document_title }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Aadhar Document</label>
                                    <input type="hidden" name="document_oldfile"
                                        value="{{ $editModeData->document_name4 }}">
                                    <input class="form-control photo" id="document-file"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file"
                                        type="file" value="0">
                                </div>
                            </div>

                            <div class="col-md-4" hidden>
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="document_expiry"
                                            placeholder="Document Expiry" name="document_expiry" type="text"
                                            value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Pan Number</label>
                                    <input type="text" class="form-control" name="document_title2"
                                        value="{{ $editModeData->document_title2 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Pan Document</label>
                                    <input type="hidden" name="document_oldfile2"
                                        value="{{ $editModeData->document_name2 }}">
                                    <input class="form-control photo" id="document-document_file2"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file2"
                                        type="file" value="0">
                                </div>
                            </div>

                            <div class="col-md-4" hidden>
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="document_expiry"
                                            placeholder="Document Expiry" name="document_expiry2" type="text"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">S.S.L.C Certificate Number</label>
                                    <input type="text" class="form-control" name="document_title3"
                                        value="{{ $editModeData->document_title3 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload S.S.L.C Certificate</label>
                                    <input type="hidden" name="document_oldfile4"
                                        value="{{ $editModeData->document_name4 }}">
                                    <input class="form-control photo" id="document-file4"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file3"
                                        type="file" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">H.S.C Certificate Number </label>
                                    <input type="text" class="form-control" name="document_title4"
                                        value="{{ $editModeData->document_title4 }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload H.S.C Certificate</label>
                                    <input type="hidden" name="document_oldfile4"
                                        value="{{ $editModeData->document_name4 }}">
                                    <input class="form-control photo" id="document-file4"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file4"
                                        type="file" value="0">
                                </div>
                            </div>
                            <div class="col-md-4" hidden>
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="document_expiry"
                                            placeholder="Document Expiry" name="document_expiry3" type="text"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">U.G Certificate Number</label>
                                    <input type="text" class="form-control" name="document_title5"
                                        value="{{ $editModeData->document_title5 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload U.G Certificate</label>
                                    <input type="hidden" name="document_oldfile4"
                                        value="{{ $editModeData->document_name5 }}">
                                    <input class="form-control photo" id="document-file4"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file5"
                                        type="file" value="0">
                                </div>
                            </div>

                            <div class="col-md-4" hidden>
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="document_expiry"
                                            placeholder="Document Expiry" name="document_expiry5" type="text"
                                            value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">P.G Certificate Number</label>
                                    <input type="text" class="form-control" name="document_title6"
                                        value="{{ $editModeData->document_title6 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload P.G Certificate</label>
                                    <input type="hidden" name="document_oldfile4"
                                        value="{{ $editModeData->document_name6 }}">
                                    <input class="form-control photo" id="document-file6"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file6"
                                        type="file" value="0">
                                </div>
                            </div>
                        </div>

                          <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Title</label>
                                        <input type="text" class="form-control" name="document_title12" value="{{ $editModeData->document_title12 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Number</label>
                                        <input type="text" class="form-control" name="document_number12" value="{{ $editModeData->document_number12 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document 7</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file12"
                                            type="file">
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Title</label>
                                        <input type="text" class="form-control" name="document_title13" value="{{ $editModeData->document_title13 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Number</label>
                                        <input type="text" class="form-control" name="document_number13" value="{{ $editModeData->document_number13 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document 8</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file13"
                                            type="file">
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Title</label>
                                        <input type="text" class="form-control" name="document_title14" value="{{ $editModeData->document_title14 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Number</label>
                                        <input type="text" class="form-control" name="document_number14" value="{{ $editModeData->document_number14 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document 9</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file14"
                                            type="file">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Title</label>
                                        <input type="text" class="form-control" name="document_title15" value="{{ $editModeData->document_title15 }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Number</label>
                                        <input type="text" class="form-control" name="document_number15" value="{{ $editModeData->document_number15 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document 10</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file15" type="file">
                                    </div>
                                </div>
                            </div>



                        <div class="row" hidden>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Work Experience</label>
                                    <input type="text" class="form-control" name="document_title7"
                                        value="{{ $editModeData->document_title7 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload Work Experience Document</label>
                                    <input class="form-control photo" id="document-file7"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file7"
                                        type="file">
                                </div>
                            </div>

                            <div class="col-md-4" hidden>
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="hidden" name="document_oldfile4"
                                            value="{{ $editModeData->document_name4 }}">
                                        <input class="form-control photo" id="document-file4"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file4"
                                            type="file" value="0">
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- <h3> Reminder Document Details</h3>
                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Passport Number</label>
                                    <input type="text" class="form-control" name="document_title8"
                                        value="{{ $editModeData->document_title8 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload Document</label>
                                    <input class="form-control photo" id="document-file8"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file8"
                                        type="file">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="expiry_date8"
                                            placeholder="Document Expiry" name="expiry_date8" type="text"
                                            value="{{ dateConvertDBtoForm($editModeData->expiry_date8) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Visa Number</label>
                                    <input type="text" class="form-control" name="document_title9"
                                        value="{{ $editModeData->document_title9 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload Document</label>
                                    <input class="form-control photo" id="document-file9"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file9"
                                        type="file">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="expiry_date9"
                                            placeholder="Document Expiry" name="expiry_date9" type="text"
                                            value="{{ dateConvertDBtoForm($editModeData->expiry_date9) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Driving Licence Number</label>
                                    <input type="text" class="form-control" name="document_title10"
                                        value="{{ $editModeData->document_title10 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload Document</label>
                                    <input class="form-control photo" id="document-file10"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file10"
                                        type="file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="expiry_date10"
                                            placeholder="Document Expiry" name="expiry_date10" type="text"
                                            value="{{ dateConvertDBtoForm($editModeData->expiry_date10) }}">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Resident Card Number</label>
                                    <input type="text" class="form-control" name="document_title11"
                                        value="{{ $editModeData->document_title11 }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload  Document</label>
                                    <input class="form-control photo" id="document-file11"
                                        accept="image/png, image/jpeg, application/pdf" name="document_file11"
                                        type="file">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control dateField" readonly required id="expiry_date11"
                                            placeholder="Document Expiry" name="expiry_date11" type="text"
                                            value="{{ dateConvertDBtoForm($editModeData->expiry_date11) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 5 Title</label>
                                        <input type="text" class="form-control" name="document_title16" value="{{ $editModeData->document_title16 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file16"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 5 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date16"
                                                placeholder="Document Expiry" name="expiry_date16" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date16) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 6 Title</label>
                                        <input type="text" class="form-control" name="document_title17" value="{{ $editModeData->document_title17 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file17"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 6 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date17"
                                                placeholder="Document Expiry" name="expiry_date17" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date17) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Title</label>
                                        <input type="text" class="form-control" name="document_title18" value="{{ $editModeData->document_title18 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file18"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date18"
                                                placeholder="Document Expiry" name="expiry_date18" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date18) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Title</label>
                                        <input type="text" class="form-control" name="document_title19" value="{{ $editModeData->document_title19 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file19"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date19"
                                                placeholder="Document Expiry" name="expiry_date19" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date19) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Title</label>
                                        <input type="text" class="form-control" name="document_title20" value="{{ $editModeData->document_title20 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file20"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date20"
                                                placeholder="Document Expiry" name="expiry_date20" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date20) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Title</label>
                                        <input type="text" class="form-control" name="document_title21" value="{{ $editModeData->document_title21 }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file21"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required id="expiry_date21"
                                                placeholder="Document Expiry" name="expiry_date21" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->expiry_date21) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>





                        <br> -->
                        <!-- <h3> Salary PayGrade</h3>

                        <hr>




                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">@lang('paygrade.gross_salary')<span
                                            class="validateRq">*</span></label>
                                    {!! Form::number(
                                        'gross_salary',
                                        Input::old('gross_salary'),
                                        $attributes = [
                                            'class' => 'form-control required gross_salary',
                                            'id' => 'gross_salary',
                                            'placeholder' => __('paygrade.gross_salary'),
                                            'min' => '0',
                                        ],
                                    ) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">@lang('paygrade.basic_salary')<span
                                            class="validateRq">*</span></label>
                                    {!! Form::number(
                                        'basic_salary',
                                        Input::old('basic_salary'),
                                        $attributes = [
                                            'class' => 'form-control required basic_salary',
                                            'readonly' => 'readonly',
                                            'id' => 'basic_salary',
                                            'placeholder' => __('paygrade.basic_salary'),
                                            'min' => '0',
                                        ],
                                    ) !!}
                                </div>
                            </div>


                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.hra')</label>

                                    <input class="form-control text hra" id="hra"
                                        placeholder="@lang('employee.hra')" name="hra" type="number"
                                        value="{{ $editModeData->hra }}">

                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.conveyance')</label>

                                    <input class="form-control text conveyance" id="conveyance"
                                        placeholder="@lang('employee.conveyance')" name="conveyance" type="number"
                                        value="{{ $editModeData->conveyance }}">

                                </div>

                            </div>
                        </div>
                        <div class="row">


                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.medical_allowance')</label>
                                    <input class="form-control text medical_allowance" id="medical_allowance"
                                        placeholder="@lang('employee.medical_allowance')" name="medical_allowance" type="number"
                                        value="{{ $editModeData->medical_allowance }}">


                                </div>
                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.shift_allowance')</label>

                                    <input class="form-control text shift_allowance" id="shift_allowance"
                                        placeholder="@lang('employee.shift_allowance')" name="shift_allowance" type="number"
                                        value="{{ $editModeData->shift_allowance }}">

                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.incentive')</label>

                                    <input class="form-control text incentive" id="incentive"
                                        placeholder="@lang('employee.incentive')" name="incentive" type="number"
                                        value="{{ $editModeData->incentive }}">


                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.variable_pay')</label>

                                    <input class="form-control text variable_pay" id="variable_pay"
                                        placeholder="@lang('employee.variable_pay')" name="variable_pay" type="number"
                                        value="{{ $editModeData->variable_pay }}">


                                </div>

                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.other_allowance')</label>
                                    <input class="form-control text other_allowance" id="other_allowance"
                                        placeholder="@lang('employee.other_allowance')" name="other_allowance" type="number"
                                        value="{{ $editModeData->other_allowance }}">


                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.deduction_of_epf')</label>

                                    <input class="form-control text deduction_of_epf" id="deduction_of_epf"
                                        placeholder="@lang('employee.deduction_of_epf')" name="deduction_of_epf" type="number"
                                        value="{{ $editModeData->deduction_of_epf }}">

                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.deduction_of_esic')</label>

                                    <input class="form-control text deduction_of_esic" id="deduction_of_esic"
                                        placeholder="@lang('employee.deduction_of_esic')" name="deduction_of_esic" type="number"
                                        value="{{ $editModeData->deduction_of_esic }}">
                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.professional_tax')</label>
                                    <input class="form-control text professional_tax" id="professional_tax"
                                        placeholder="@lang('employee.professional_tax')" name="professional_tax" type="number"
                                        value="{{ $editModeData->professional_tax }}">
                                </div>

                            </div>
                        </div>
                        <div class="row">




                        </div>
                        <div class="row">

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.medical_insurance')</label>

                                    <input class="form-control text medical_insurance" id="medical_insurance"
                                        placeholder="@lang('employee.medical_insurance')" name="medical_insurance" type="number"
                                        value="{{ $editModeData->medical_insurance }}">


                                </div>

                            </div>
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.employer_esic')</label>
                                    <input class="form-control employer_esic readonly-text" id="employer_esic"
                                        placeholder="@lang('employee.employer_esic')" name="employer_esic" type="number"
                                        value="{{ $editModeData->employer_esic }}">

                                </div>

                            </div>


                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.net_pay')</label>
                                    <input class="form-control net_pay readonly-text" id="net_pay"
                                        placeholder="@lang('employee.net_pay')" name="net_pay" type="number"
                                        value="{{ $editModeData->net_pay }}">

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.monthly_ctc')</label>
                                    <input class="form-control monthly_ctc readonly-text" id="monthly_ctc"
                                        placeholder="@lang('employee.monthly_ctc')" name="monthly_ctc" type="number"
                                        value="{{ $editModeData->monthly_ctc }}">

                                </div>

                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-3">

                                <div class="form-group">

                                    <label for="exampleInput">@lang('employee.ctc')</label>
                                    <input class="form-control ctc readonly-text" id="ctc"
                                        placeholder="@lang('employee.ctc')" name="ctc" type="number"
                                        value="{{ $editModeData->ctc }}">

                                </div>

                            </div>
                        </div>

                         </div> -->



                        <h3 class="box-title" hidden>@lang('employee.educational_qualification')</h3>

                        <hr hidden>

                        <div class="education_qualification_append_div">

                            @if (isset($editModeData) && count($educationQualificationEditModeData) > 0)
                                @foreach ($educationQualificationEditModeData as $educationQualificationValue)
                                    <div class="education_qualification_row_element">

                                        <input class="educationQualification_cid" id="educationQualification_cid"
                                            name="educationQualification_cid[]" type="hidden"
                                            value="{{ $educationQualificationValue->employee_education_qualification_id }}">

                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.institute')<span
                                                            class="validateRq">*</span></label>

                                                    <select name="institute[]" class="form-control institute">

                                                        <option value="">--- @lang('common.please_select') ---
                                                        </option>

                                                        <option value="Board"
                                                            @if ($educationQualificationValue->institute == 'Board') {{ 'selected' }} @endif>
                                                            @lang('employee.board')</option>

                                                        <option value="University"
                                                            @if ($educationQualificationValue->institute == 'University') {{ 'selected' }} @endif>
                                                            @lang('employee.university')</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.board') /
                                                        @lang('employee.university')<span class="validateRq">*</span></label>

                                                    <input type="text" name="board_university[]"
                                                        class="form-control board_university" id="board_university"
                                                        placeholder="@lang('employee.board') / @lang('employee.university')"
                                                        value="{{ $educationQualificationValue->board_university }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.degree')<span
                                                            class="validateRq">*</span></label>

                                                    <input type="text" name="degree[]"
                                                        class="form-control degree required" id="degree"
                                                        placeholder="Example: B.Sc. Engr.(Bachelor of Science in Engineering)"
                                                        value="{{ $educationQualificationValue->degree }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3" hidden>

                                                <label for="exampleInput">@lang('employee.passing_year')<span
                                                        class="validateRq">*</span></label>

                                                <div class="input-group">

                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar-o"></i></span>

                                                    <input type="text" name="passing_year[]"
                                                        class="form-control yearPicker required" id="passing_year"
                                                        placeholder="@lang('employee.passing_year')"
                                                        value="{{ $educationQualificationValue->passing_year }}">

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.result')</label>

                                                    <select name="result[]" class="form-control result">

                                                        <option value="">--- @lang('common.please_select') ---
                                                        </option>

                                                        <option value="First class"
                                                            @if ($educationQualificationValue->result == 'First class') {{ 'selected' }} @endif>
                                                            First class</option>

                                                        <option value="Second class"
                                                            @if ($educationQualificationValue->result == 'Second class') {{ 'selected' }} @endif>
                                                            Second class</option>

                                                        <option value="Third class"
                                                            @if ($educationQualificationValue->result == 'Third class') {{ 'selected' }} @endif>
                                                            Third class</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.gpa') /
                                                        @lang('employee.cgpa')</label>

                                                    <input type="text" name="cgpa[]" class="form-control cgpa"
                                                        id="cgpa" placeholder="Example: 5.00,4.63"
                                                        value="{{ $educationQualificationValue->cgpa }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3"></div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <input type="button"
                                                        class="form-control btn btn-danger deleteEducationQualification appendBtnColor"
                                                        style="margin-top: 17px" value="@lang('common.delete')">

                                                </div>

                                            </div>

                                        </div>

                                        <hr hidden>

                                    </div>
                                @endforeach
                            @endif

                        </div>

                        <div class="row" hidden>

                            <div class="col-md-9"></div>

                            <div class="col-md-3">
                                <div class="form-group"><input id="addEducationQualification" type="button"
                                        class="form-control btn btn-success appendBtnColor"
                                        value="@lang('employee.add_educational_qualification')"></div>
                            </div>

                        </div>

                        <br>

                        <h3 class="box-title" hidden>@lang('employee.professional_experience')</h3>

                        <hr hidden>

                        <div class="experience_append_div">

                            @if (isset($editModeData) && count($experienceEditModeData) > 0)
                                @foreach ($experienceEditModeData as $experienceValue)
                                    <div class="experience_row_element">

                                        <input class="employee_experience_id" id="employee_experience_id"
                                            name="employeeExperience_cid[]" type="hidden"
                                            value="{{ $experienceValue->employee_experience_id }}">

                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.organization_name')<span
                                                            class="validateRq">*</span></label>

                                                    <input type="text" name="organization_name[]"
                                                        class="form-control organization_name"
                                                        id="organization_name" placeholder="@lang('employee.organization_name')"
                                                        value="{{ $experienceValue->organization_name }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.designation')<span
                                                            class="validateRq">*</span></label>

                                                    <input type="text" name="designation[]"
                                                        class="form-control designation" id="designation"
                                                        placeholder="@lang('employee.designation')"
                                                        value="{{ dateConvertDBtoForm($experienceValue->designation) }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <label for="exampleInput">@lang('common.from_date')<span
                                                        class="validateRq">*</span></label>

                                                <div class="input-group">

                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar"></i></span>

                                                    <input type="text" name="from_date[]"
                                                        class="form-control dateField" id="from_date"
                                                        placeholder="@lang('common.from_date')"
                                                        value="{{ dateConvertDBtoForm($experienceValue->from_date) }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <label for="exampleInput">@lang('common.to_date')<span
                                                        class="validateRq">*</span></label>

                                                <div class="input-group">

                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar"></i></span>

                                                    <input type="text" name="to_date[]"
                                                        class="form-control dateField" id="to_date"
                                                        placeholder="@lang('common.to_date')"
                                                        value="{{ dateConvertDBtoForm($experienceValue->to_date) }}">

                                                </div>

                                            </div>

                                        </div>



                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.responsibility')<span
                                                            class="validateRq">*</span></label>

                                                    <textarea name="responsibility[]" class="form-control responsibility" placeholder="@lang('employee.responsibility')"
                                                        cols="30" rows="2" required>{{ $experienceValue->responsibility }}</textarea>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <label for="exampleInput">@lang('employee.skill')<span
                                                            class="validateRq">*</span></label>

                                                    <textarea name="skill[]" class="form-control skill" placeholder="@lang('employee.skill')" cols="30"
                                                        rows="2">{{ $experienceValue->skill }}</textarea>

                                                </div>

                                            </div>

                                            <div class="col-md-3"></div>

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <input type="button"
                                                        class="form-control btn btn-danger deleteExperience appendBtnColor"
                                                        style="margin-top: 17px" value="@lang('common.delete')">

                                                </div>

                                            </div>

                                        </div>

                                        <hr hidden>

                                    </div>
                                @endforeach
                            @endif

                        </div>

                        <div class="row" hidden>

                            <div class="col-md-9"></div>

                            <div class="col-md-3">
                                <div class="form-group"><input id="addExperience" type="button"
                                        class="form-control btn btn-success appendBtnColor"
                                        value="@lang('employee.add_professional_experience')"></div>
                            </div>

                        </div>

                        <div class="form-actions">

                            <div class="row">

                                <div class="col-md-12 ">

                                    <button type="submit" class="btn btn-info btn_style"><i
                                            class="fa fa-pencil"></i>
                                        @lang('common.update')</button>

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

</div>



<div class="row_element1" style="display: none;">

    <input name="educationQualification_cid[]" type="hidden">

    <div class="row">

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.institute')<span class="validateRq">*</span></label>

                <select name="institute[]" class="form-control institute">

                    <option value="">--- @lang('common.please_select') ---</option>

                    <option value="Board">@lang('employee.board')</option>

                    <option value="University">@lang('employee.university')</option>

                </select>

            </div>

        </div>

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.board') / @lang('employee.university')<span
                        class="validateRq">*</span></label>

                <input type="text" name="board_university[]" class="form-control board_university"
                    id="board_university" placeholder="@lang('employee.board') / @lang('employee.university')">

            </div>

        </div>

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.degree')<span class="validateRq">*</span></label>

                <input type="text" name="degree[]" class="form-control degree required" id="degree"
                    placeholder="Example: B.Sc. Engr.(Bachelor of Science in Engineering)">

            </div>

        </div>

        <div class="col-md-3">

            <label for="exampleInput">@lang('employee.passing_year')<span class="validateRq">*</span></label>

            <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>

                <input type="text" name="passing_year[]" class="form-control yearPicker required"
                    id="passing_year" placeholder="@lang('employee.passing_year')">

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.result')</label>

                <select name="result[]" class="form-control result">

                    <option value="">--- @lang('common.please_select') ---</option>

                    <option value="First class">First class</option>

                    <option value="Second class">Second class</option>

                    <option value="Third class">Third class</option>

                </select>

            </div>

        </div>

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.gpa') / @lang('employee.cgpa')</label>

                <input type="text" name="cgpa[]" class="form-control cgpa" id="cgpa"
                    placeholder="Example: 5.00,4.63">

            </div>

        </div>

        <div class="col-md-3"></div>

        <div class="col-md-3">

            <div class="form-group">

                <input type="button"
                    class="form-control btn btn-danger deleteEducationQualification appendBtnColor"
                    style="margin-top: 17px" value="@lang('common.delete')">

            </div>

        </div>

    </div>

    <hr>

</div>



<div class="row_element2" style="display: none;">

    <input name="employeeExperience_cid[]" type="hidden">

    <div class="row">

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.organization_name')<span class="validateRq">*</span></label>

                <input type="text" name="organization_name[]" class="form-control organization_name"
                    id="organization_name" placeholder="@lang('employee.organization_name')">

            </div>

        </div>

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.designation')<span class="validateRq">*</span></label>

                <input type="text" name="designation[]" class="form-control designation" id="designation"
                    placeholder="@lang('employee.designation')">

            </div>

        </div>

        <div class="col-md-3">

            <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>

            <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                <input type="text" name="from_date[]" class="form-control dateField" id="from_date"
                    placeholder="@lang('common.from_date')">

            </div>

        </div>

        <div class="col-md-3">

            <label for="exampleInput">@lang('common.to_date')<span class="validateRq">*</span></label>

            <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                <input type="text" name="to_date[]" class="form-control dateField" id="to_date"
                    placeholder="@lang('common.to_date')">

            </div>

        </div>

    </div>



    <div class="row">

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.responsibility')<span class="validateRq">*</span></label>

                <textarea name="responsibility[]" class="form-control responsibility" placeholder="@lang('employee.responsibility')"
                    cols="30" rows="2"></textarea>

            </div>

        </div>

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.skill')<span class="validateRq">*</span></label>

                <textarea name="skill[]" class="form-control skill" placeholder="@lang('employee.skill')" cols="30"
                    rows="2"></textarea>

            </div>

        </div>

        <div class="col-md-3"></div>

        <div class="col-md-3">

            <div class="form-group">

                <input type="button" class="form-control btn btn-danger deleteExperience appendBtnColor"
                    style="margin-top: 17px" value="@lang('common.delete')">

            </div>

        </div>

    </div>

    <hr>

</div>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {



        $('#addEducationQualification').click(function() {

            $('.education_qualification_append_div').append(
                '<div class="education_qualification_row_element">' + $('.row_element1').html() +
                '</div>');

        });



        $('#addExperience').click(function() {

            $('.experience_append_div').append('<div class="experience_row_element">' + $(
                '.row_element2').html() + '</div>');

        });



        $(document).on("click", ".deleteEducationQualification", function() {

            $(this).parents('.education_qualification_row_element').remove();

            var deletedID = $(this).parents('.education_qualification_row_element').find(
                '.educationQualification_cid').val();

            if (deletedID) {

                var prevDelId = $('#delete_education_qualifications_cid').val();

                if (prevDelId) {

                    $('#delete_education_qualifications_cid').val(prevDelId + ',' + deletedID);

                } else {

                    $('#delete_education_qualifications_cid').val(deletedID);

                }

            }

        });



        $(document).on("click", ".deleteExperience", function() {

            $(this).parents('.experience_row_element').remove();

            var deletedID = $(this).parents('.experience_row_element').find('.employee_experience_id')
                .val();

            if (deletedID) {

                var prevDelId = $('#delete_experiences_cid').val();

                if (prevDelId) {

                    $('#delete_experiences_cid').val(prevDelId + ',' + deletedID);

                } else {

                    $('#delete_experiences_cid').val(deletedID);

                }

            }

        });



        $(document).on('change', '.pay_grade_id', function() {

            var data = $('.pay_grade_id').val();

            if (data) {

                $('.hourly_pay_grade_id').val('');

                $('.pay_grade_id').attr('required', false);

                $('.hourly_pay_grade_id').attr('required', false);

            } else {

                $('.pay_grade_id').attr('required', true);

                $('.hourly_pay_grade_id').attr('required', true);

            }

        });



        $(document).on('change', '.hourly_pay_grade_id', function() {

            var data = $('.hourly_pay_grade_id').val();

            if (data) {

                $('.pay_grade_id').val('');

                $('.pay_grade_id').attr('required', false);

                $('.hourly_pay_grade_id').attr('required', false);

            } else {

                $('.pay_grade_id').attr('required', true);

                $('.hourly_pay_grade_id').attr('required', true);

            }

        });



    });
</script>
@endsection
