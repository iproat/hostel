@extends('admin.master')

@section('content')

@section('title')
    @lang('employee.add_employee')
@endsection

<style>
    .appendBtnColor {

        color: #fff;

        font-weight: 700;

    }

    .readonly-text {
        pointer-events: none;
        background: #EEEEEE;
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

                        {{ Form::open(['route' => 'employee.store', 'enctype' => 'multipart/form-data', 'id' => 'employeeForm']) }}

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
                                                    @if ($value->role_id == old('role_id')) {{ 'selected' }} @endif>
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
                                            value="{{ old('user_name') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <label for="password">@lang('employee.password')<span class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <div class="input-group-addon"><i class="ti-lock"></i></div>

                                        <input class="form-control required password" required id="password"
                                            placeholder="@lang('employee.password')" name="password" type="password">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <label for="password_confirmation">@lang('employee.confirm_password')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <div class="input-group-addon"><i class="ti-lock"></i></div>

                                        <input class="form-control required password_confirmation" required
                                            id="password_confirmation" placeholder="@lang('employee.confirm_password')"
                                            name="password_confirmation" type="password">

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

                                        <input class="form-control required first_name" required id="first_name"
                                            placeholder="@lang('employee.first_name')" name="first_name" type="text"
                                            value="{{ old('first_name') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.last_name')</label>

                                        <input class="form-control last_name" id="last_name"
                                            placeholder="@lang('employee.last_name')" name="last_name" type="text"
                                            value="{{ old('last_name') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.finger_print_no')<span
                                                class="validateRq">*</span></label>

                                        <input class="form-control number finger_id" required id="finger_id"
                                            placeholder="@lang('employee.finger_print_no')" name="finger_id" type="text"
                                            value="{{ old('finger_id') }}">

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
                                                    @if ($value->employee_id == old('employee_id')) {{ 'selected' }} @endif>
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

                                        <select name="department_id" class="form-control department_id  select2"
                                            required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if ($value->department_id == old('department_id')) {{ 'selected' }} @endif>
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

                                        <select name="designation_id" class="form-control department_id select2"
                                            required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($designationList as $value)
                                                <option value="{{ $value->designation_id }}"
                                                    @if ($value->designation_id == old('designation_id')) {{ 'selected' }} @endif>
                                                    {{ $value->designation_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('branch.branch_name')<span
                                                class="validateRq">*</span></label>

                                        <select name="branch_id" class="form-control branch_id select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($branchList as $value)
                                                <option value="{{ $value->branch_id }}"
                                                    @if ($value->branch_id == old('branch_id')) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.blood_group')</label>

                                        <input class="form-control blood_group" id="blood_group"
                                            placeholder="@lang('employee.blood_group')" name="blood_group" type="text"
                                            value="{{ old('blood_group') }}">

                                    </div>

                                </div>
                                {{-- <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('work_shift.work_shift_name')<span
                                                class="validateRq">*</span></label>

                                        <select name="work_shift_id" class="form-control work_shift_id select2"
                                            required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($workShiftList as $value)
                                                <option value="{{ $value->work_shift_id }}"
                                                    @if ($value->work_shift_id == old('work_shift_id')) {{ 'selected' }} @endif>
                                                    {{ $value->shift_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div> --}}

                            </div>



                            <div class="row">
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('common.status')<span
                                                class="validateRq">*</span></label>

                                        <select name="status" class="form-control status select2" required>

                                            <option value="1"
                                                @if ('1' == old('status')) {{ 'selected' }} @endif>
                                                @lang('common.active')</option>

                                            <option value="2"
                                                @if ('2' == old('status')) {{ 'selected' }} @endif>
                                                @lang('common.inactive')</option>

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
                                <!-- <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.montly_paygrade')<span
                                                class="validateRq">*</span></label>

                                        <select name="pay_grade_id" class="form-control pay_grade_id required"
                                            required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($payGradeList as $value)
<option value="{{ $value->pay_grade_id }}"
                                                    @if ($value->pay_grade_id == old('pay_grade_id')) {{ 'selected' }} @endif>
                                                    {{ $value->pay_grade_name }}</option>
@endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.hourly_paygrade')</label>

                                        <select name="hourly_salaries_id"
                                            class="form-control hourly_pay_grade_id">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            @foreach ($hourlyPayGradeList as $value)
<option value="{{ $value->hourly_salaries_id }}"
                                                    @if ($value->hourly_salaries_id == old('hourly_salaries_id')) {{ 'selected' }} @endif>
                                                    {{ $value->hourly_grade }}</option>
@endforeach

                                        </select>

                                    </div>

                                </div> -->

                                <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.personal_email')</label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                                        <input class="form-control personal_email" id="personal_email"
                                            placeholder="@lang('employee.personal_email')" name="personal_email" type="email"
                                            value="{{ old('personal_email') }}">

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <label for="exampleInput">@lang('employee.official_email')</label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                                        <input class="form-control official_email" id="official_email"
                                            placeholder="@lang('employee.official_email')" name="official_email" type="email"
                                            value="{{ old('official_email') }}">

                                    </div>

                                </div>

                            </div>
                            <div class="row">



                                {{-- <div class="col-md-3" hidden>

                                    <label for="exampleInput">@lang('employee.phone')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input class="form-control number phone" id="phone" required
                                            placeholder="@lang('employee.phone')" name="phone" type="number"
                                            value="{{ old('phone') }}">

                                    </div>

                                </div> --}}
                                <div class="col-md-3" hidden>

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.faith')</label>

                                        <input class="form-control faith" id="faith"
                                            placeholder="@lang('employee.faith')" name="faith" type="text"
                                            value="{{ old('faith') }}">

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.gender')<span
                                                class="validateRq">*</span></label>

                                        <select name="gender" class="form-control gender select2" required>

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            <option value="Male"
                                                @if ('Male' == old('gender')) {{ 'selected' }} @endif>
                                                @lang('employee.male')</option>

                                            <option value="Female"
                                                @if ('Female' == old('gender')) {{ 'selected' }} @endif>
                                                @lang('employee.female')</option>
                                            <option value="NoDisclosure"
                                                @if ('NoDisclosure' == old('gender')) {{ 'selected' }} @endif>
                                                @lang('employee.no_disclosure')</option>

                                        </select>

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.address')</label>

                                        <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                            rows="2" name="address">{{ old('address') }}</textarea>
                                    </div>
                                </div>


                            </div>



                            <div class="row" hidden>
                                <div class="col-md-3" hidden>

                                    <label for="exampleInput">@lang('employee.date_of_birth')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                        <input class="form-control date_of_birth dateField" readonly required
                                            id="date_of_birth" placeholder="@lang('employee.date_of_birth')" name="date_of_birth"
                                            type="text" value="{{ old('date_of_birth') }}">

                                    </div>

                                </div>

                                <div class="col-md-3" hidden>

                                    <label for="exampleInput">@lang('employee.date_of_joining')<span
                                            class="validateRq">*</span></label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                        <input class="form-control date_of_joining dateField" readonly required
                                            id="date_of_joining" placeholder="@lang('employee.date_of_joining')"
                                            name="date_of_joining" type="text"
                                            value="{{ old('date_of_joining') }}">

                                    </div>

                                </div>
                                <div class="col-md-3" hidden>

                                    <label for="exampleInput">@lang('employee.date_of_leaving')</label>

                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                        <input class="form-control  date_of_leaving dateField" readonly
                                            id="date_of_leaving" placeholder="@lang('employee.date_of_leaving')"
                                            name="date_of_leaving" type="text"
                                            value="{{ old('date_of_leaving') }}">

                                    </div>

                                </div>
                                <div class="col-md-3" hidden>

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.marital_status')</label>

                                        <select name="marital_status" class="form-control status required select2">

                                            <option value="">--- @lang('common.please_select') ---</option>

                                            <option value="Unmarried"
                                                @if ('Unmarried' == old('marital_status')) {{ 'selected' }} @endif>
                                                @lang('employee.unmarried')</option>

                                            <option value="Married"
                                                @if ('Married' == old('marital_status')) {{ 'selected' }} @endif>
                                                @lang('employee.married')</option>

                                        </select>

                                    </div>

                                </div>

                            </div>



                            <div class="row" hidden>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.address')</label>

                                        <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                            rows="2" name="address">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <!--
                            <h3> Card Details</h3>

                            <hr> -->
                            <!-- <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('employee.pf_account_number')</label>
                                        <input class="form-control pf_account_number" id="pf_account_number"
                                            placeholder="@lang('employee.pf_account_number')" name="pf_account_number" type="number"
                                            value="{{ old('pf_account_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.esi_card_number')</label>

                                        <input class="form-control esi_card_number" id="esi_card_number"
                                            placeholder="@lang('employee.esi_card_number')" name="esi_card_number" type="number"
                                            value="{{ old('esi_card_number') }}">

                                    </div>

                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 1 Title</label>
                                        <input class="form-control card_title1" id="card_title1"
                                            placeholder="" name="card_title1" type="text"
                                            value="{{ old('card_title1') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 1 Number</label>

                                        <input class="form-control card_number1" id="card_number1"
                                            placeholder="" name="card_number1" type="number"
                                            value="{{ old('card_number1') }}">

                                    </div>

                                </div>


                            </div>


                            
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 2 Title</label>
                                        <input class="form-control card_title2" id="card_title2"
                                            placeholder="" name="card_title2" type="text"
                                            value="{{ old('card_title2') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 2 Number</label>

                                        <input class="form-control card_number2" id="card_number2"
                                            placeholder="" name="card_number2" type="number"
                                            value="{{ old('card_number2') }}">

                                    </div>

                                </div>

                                      <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 3 Title</label>
                                        <input class="form-control card_title3" id="card_title3"
                                            placeholder="" name="card_title3" type="text"
                                            value="{{ old('card_title3') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 3 Number</label>

                                        <input class="form-control card_number3" id="card_number3"
                                            placeholder="" name="card_number3" type="number"
                                            value="{{ old('card_number3') }}">

                                    </div>

                                </div>
                            </div>
                       
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 4 Title</label>
                                        <input class="form-control card_title4" id="card_title4"
                                            placeholder="" name="card_title4" type="text"
                                            value="{{ old('card_title4') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 4 Number</label>

                                        <input class="form-control card_number4" id="card_number4"
                                            placeholder="" name="card_number4" type="number"
                                            value="{{ old('card_number4') }}">

                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Card 5 Title</label>
                                        <input class="form-control card_title5" id="card_title5"
                                            placeholder="" name="card_title5" type="text"
                                            value="{{ old('card_title5') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">Card 5 Number</label>

                                        <input class="form-control card_number5" id="card_number5"
                                            placeholder="" name="card_number5" type="number"
                                            value="{{ old('card_number5') }}">

                                    </div>

                                </div>

                            </div>
                            
                            <br> -->

                            <!-- <h3> Emergency Contact Details</h3>

                            <hr>

                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.emergency_contact')</label>

                                        <input class="form-control emergency_contact" id="emergency_contact"
                                            placeholder="@lang('employee.emergency_contact')" name="emergency_contact" type="number"
                                            value="{{ old('emergency_contact') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.contact_person_name')</label>

                                        <input class="form-control contact_person_name" id="contact_person_name"
                                            placeholder="@lang('employee.contact_person_name')" name="contact_person_name"
                                            type="text" value="{{ old('contact_person_name') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.relation_of_contact_person')</label>

                                        <input class="form-control relation_of_contact_person"
                                            id="relation_of_contact_person" placeholder="@lang('employee.relation_of_contact_person')"
                                            name="relation_of_contact_person" type="text"
                                            value="{{ old('relation_of_contact_person') }}">

                                    </div>

                                </div>



                            </div>

                            <h3> Document Details</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Aadhar Number</label>
                                        <input type="text" class="form-control" name="document_title">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Aadhar Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required
                                                id="document_expiry" placeholder="Document Expiry"
                                                name="document_expiry" type="text" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Pan Number</label>
                                        <input type="text" class="form-control" name="document_title2">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Pan Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file2"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required
                                                id="document_expiry" placeholder="Document Expiry"
                                                name="document_expiry2" type="text" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">S.S.L.C Certificate Number</label>
                                        <input type="text" class="form-control" name="document_title3">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload S.S.L.C Certificate</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file3"
                                            type="file">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">H.S.C Certificate Number </label>
                                        <input type="text" class="form-control" name="document_title4">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload H.S.C Certificate</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file4"
                                            type="file">
                                    </div>
                                </div>
                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required
                                                id="document_expiry" placeholder="Document Expiry"
                                                name="document_expiry3" type="text" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">U.G Certificate Number</label>
                                        <input type="text" class="form-control" name="document_title5">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload U.G Certificate</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file5"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required
                                                id="document_expiry" placeholder="Document Expiry"
                                                name="document_expiry5" type="text" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">P.G Certificate Number</label>
                                        <input type="text" class="form-control" name="document_title6">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload P.G Certificate</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file6"
                                            type="file">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Title</label>
                                        <input type="text" class="form-control" name="document_title12">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Number</label>
                                        <input type="text" class="form-control" name="document_number12">
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
                                        <input type="text" class="form-control" name="document_title13">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Number</label>
                                        <input type="text" class="form-control" name="document_number13">
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
                                        <input type="text" class="form-control" name="document_title14">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Number</label>
                                        <input type="text" class="form-control" name="document_number14">
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
                                        <input type="text" class="form-control" name="document_title15">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Number</label>
                                        <input type="text" class="form-control" name="document_number15">
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
                                        <input type="text" class="form-control" name="document_title7">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Work Experience Document</label>
                                        <input class="form-control photo" id="document-file"
                                            accept="image/png, image/jpeg, application/pdf" name="document_file7"
                                            type="file">
                                    </div>
                                </div>

                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control dateField" readonly required
                                                id="document_expiry" placeholder="Document Expiry"
                                                name="document_expiry5" type="text" value="">
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
                                        <input type="text" class="form-control" name="document_title8">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Visa Number</label>
                                        <input type="text" class="form-control" name="document_title9">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Driving Licence Number</label>
                                        <input type="text" class="form-control" name="document_title10">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Resident Card Number</label>
                                        <input type="text" class="form-control" name="document_title11">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Document</label>
                                        <input class="form-control photo" id="document-file"
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 5 Title</label>
                                        <input type="text" class="form-control" name="document_title16">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 6 Title</label>
                                        <input type="text" class="form-control" name="document_title17">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 7 Title</label>
                                        <input type="text" class="form-control" name="document_title18">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 8 Title</label>
                                        <input type="text" class="form-control" name="document_title19">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 9 Title</label>
                                        <input type="text" class="form-control" name="document_title20">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">Document 10 Title</label>
                                        <input type="text" class="form-control" name="document_title21">
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
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div> -->





                            <!-- <h3 class="box-title" hidden>@lang('employee.educational_qualification')</h3>

                            <hr hidden>

                            <div class="education_qualification_append_div" hidden>



                            </div>

                            <div class="row" hidden>

                                <div class="col-md-9"></div>

                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input id="addEducationQualification" type="button"
                                            class="form-control btn btn-success appendBtnColor"
                                            value="@lang('employee.add_educational_qualification')">
                                    </div>
                                </div>

                            </div>

                        </div> -->



                            <!-- <h3 class="box-title" hidden>@lang('employee.professional_experience')</h3>

                        <hr hidden>

                        <div class="experience_append_div" hidden>



                        </div>

                        <div class="row" hidden>

                            <div class="col-md-9"></div>

                            <div class="col-md-3">
                                <div class="form-group"><input id="addExperience" type="button"
                                        class="form-control btn btn-success appendBtnColor"
                                        value="@lang('employee.add_professional_experience')"></div>
                            </div>

                        </div> -->


                            <!-- <h3> Salary PayGrade</h3>

                        <hr>


                        <div class="paygrade_append_div">

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
                                <div class="col-md-3" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('paygrade.percentage_of_basic')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control percentage_of_basic select2  required"
                                            name="percentage_of_basic">
                                            <?php
                                            $i = 50;
                                            $selected = 50;
                                            echo '<option value="' . $i . '" ' . $selected . '>' . $i . '%</option>';
                                            ?>
                                        </select>
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

                                <div class="col-md-3" hidden>
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('paygrade.over_time_rate') (@lang('paygrade.per_hour'))</label>
                                        {!! Form::number(
                                            'overtime_rate',
                                            Input::old('overtime_rate'),
                                            $attributes = [
                                                'class' => 'form-control overtime_rate',
                                                'id' => 'overtime_rate',
                                                'placeholder' => __('paygrade.over_time_rate'),
                                                'min' => '0',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.hra')</label>

                                        <input class="form-control hra" id="hra"
                                            placeholder="@lang('employee.hra')" name="hra" type="number"
                                            value="{{ old('hra') }}" readonly>

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.conveyance')</label>

                                        <input class="form-control conveyance" id="conveyance"
                                            placeholder="@lang('employee.conveyance')" name="conveyance" type="number"
                                            value="{{ old('conveyance') }}">

                                    </div>

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.medical_allowance')</label>
                                        <input class="form-control medical_allowance" id="medical_allowance"
                                            placeholder="@lang('employee.medical_allowance')" name="medical_allowance" type="number"
                                            value="{{ old('medical_allowance') }}">

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.shift_allowance')</label>

                                        <input class="form-control shift_allowance" id="shift_allowance"
                                            placeholder="@lang('employee.shift_allowance')" name="shift_allowance" type="number"
                                            value="{{ old('shift_allowance') }}">

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.incentive')</label>

                                        <input class="form-control incentive" id="incentive"
                                            placeholder="@lang('employee.incentive')" name="incentive" type="number"
                                            value="{{ old('incentive') }}">

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.variable_pay')</label>

                                        <input class="form-control variable_pay" id="variable_pay"
                                            placeholder="@lang('employee.variable_pay')" name="variable_pay" type="number"
                                            value="{{ old('variable_pay') }}">

                                    </div>

                                </div>

                            </div>
                            <div class="row">



                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.other_allowance')</label>
                                        <input class="form-control other_allowance" id="other_allowance"
                                            placeholder="@lang('employee.other_allowance')" name="other_allowance" type="number"
                                            value="{{ old('other_allowance') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.deduction_of_epf')</label>

                                        <input class="form-control deduction_of_epf" id="deduction_of_epf"
                                            placeholder="@lang('employee.deduction_of_epf')" name="deduction_of_epf" type="number"
                                            value="{{ old('deduction_of_epf') }}" readonly>

                                    </div>

                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.deduction_of_esic')</label>

                                        <input class="form-control deduction_of_esic" id="deduction_of_esic"
                                            placeholder="@lang('employee.deduction_of_esic')" name="deduction_of_esic" type="number"
                                            value="{{ old('deduction_of_esic') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.professional_tax')</label>
                                        <input class="form-control professional_tax" id="professional_tax"
                                            placeholder="@lang('employee.professional_tax')" name="professional_tax" type="number"
                                            value="{{ old('professional_tax') }}" readonly>

                                    </div>

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.medical_insurance')</label>

                                        <input class="form-control medical_insurance" id="medical_insurance"
                                            placeholder="@lang('employee.medical_insurance')" name="medical_insurance" type="number"
                                            value="{{ old('medical_insurance') }}">

                                    </div>

                                </div>

                                <div class="col-md-3" hidden>

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.total_allowance')</label>
                                        <input class="form-control total_allowance readonly-text" id="total_allowance"
                                            placeholder="@lang('employee.total_allowance')" name="total_allowance" type="number"
                                            value="{{ old('total_allowance') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3" hidden>

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.total_deduction')</label>
                                        <input class="form-control total_deduction readonly-text" id="total_deduction"
                                            placeholder="@lang('employee.total_deduction')" name="total_deduction" type="number"
                                            value="{{ old('total_deduction') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.employer_esic')</label>
                                        <input class="form-control employer_esic readonly-text" id="employer_esic"
                                            placeholder="@lang('employee.employer_esic')" name="employer_esic" type="number"
                                            value="{{ old('employer_esic') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.net_pay')</label>
                                        <input class="form-control net_pay readonly-text" id="net_pay"
                                            placeholder="@lang('employee.net_pay')" name="net_pay" type="number"
                                            value="{{ old('net_pay') }}" readonly>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.monthly_ctc')</label>
                                        <input class="form-control monthly_ctc readonly-text" id="monthly_ctc"
                                            placeholder="@lang('employee.monthly_ctc')" name="monthly_ctc" type="number"
                                            value="{{ old('monthly_ctc') }}">

                                    </div>

                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('employee.ctc')</label>
                                        <input class="form-control ctc readonly-text" id="ctc"
                                            placeholder="@lang('employee.ctc')" name="ctc" type="number"
                                            value="{{ old('ctc') }}" readonly>
                                    </div>
                                </div>
                            </div> -->


                            <div class="form-actions">
                                <br>
                                <div class="row">
                                    <div class="col-md-12 text-center ">
                                        <button type="submit" class="btn btn-info btn_style"><i
                                                class="fa fa-check"></i>
                                            @lang('common.save')</button>
                                        {{-- <div class="col-lg-5 col-sm-8 col-md-4 col-xs-12" hidden>
                                            <a href="{{ route('payGrade.create') }}"
                                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        @lang('paygrade.add_pay_grade')</a>
                                    </div> --}}
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



<div class="row_element3" style="display: none;">

    <div class="row ">
        <div class="col-md-12">
            <div class="panel panel-info">
                <!-- <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div> -->
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['payGrade.update', $editModeData->pay_grade_id], 'method' => 'PUT', 'files' => 'true']) }}
                        @else
                            {{ Form::open(['route' => 'payGrade.store', 'enctype' => 'multipart/form-data', 'id' => 'payGradeForm']) }}
                        @endif

                        <div class="form-body">

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-label="Close"><span aria-hidden="true"> </span></button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true"> </button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true"> </button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

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
                                        <label for="exampleInput">@lang('employee.pf_account_number')</label>
                                        <input class="form-control pf_account_number" id="pf_account_number"
                                            placeholder="@lang('employee.pf_account_number')" name="pf_account_number"
                                            type="text" value="{{ old('pf_account_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label for="exampleInput">@lang('employee.esi_card_number')</label>

                                        <input class="form-control esi_card_number" id="esi_card_number"
                                            placeholder="@lang('employee.esi_card_number')" name="esi_card_number"
                                            type="text" value="{{ old('esi_card_number') }}">

                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('paygrade.allowance')<span
                                                class="validateRq">*</span></label>
                                        <table border="1" style="border: 1px solid #ddd;" class="table">
                                            <thead class="thead-bar">
                                                <tr>
                                                    <th>
                                                        <div class="checkbox checkbox-info">
                                                            <input class="inputCheckbox checkAllAllowance"
                                                                type="checkbox" id="inlineCheckbox" checked>
                                                            <label>@lang('role.select_all')</label>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($allowances as $key => $allowance)
                                                    <tr>
                                                        <td>
                                                            <div class="checkbox checkbox-info">
                                                                @if (isset($sortedPayGradeWiseAllowanceData))
                                                                    <?php
                                                                    $ifStoredInAllowance = array_search($allowance->allowance_id, array_column($sortedPayGradeWiseAllowanceData, 'allowance_id'));
                                                                    ?>
                                                                    @if (gettype($ifStoredInAllowance) == 'integer')
                                                                        <input class="allowanceInputCheckbox"
                                                                            type="checkbox"
                                                                            id="inlineCheckboxAllowance{{ $key }}"
                                                                            checked name="allowance_id[]"
                                                                            value="{{ $allowance->allowance_id }}">
                                                                        <label
                                                                            for="inlineCheckboxAllowance{{ $key }}">{{ $allowance->allowance_name }}</label>
                                                                    @else
                                                                        <input class="allowanceInputCheckbox"
                                                                            type="checkbox"
                                                                            id="inlineCheckboxAllowance{{ $key }}"
                                                                            name="allowance_id[]"
                                                                            value="{{ $allowance->allowance_id }}">
                                                                        <label
                                                                            for="inlineCheckboxAllowance{{ $key }}">{{ $allowance->allowance_name }}</label>
                                                                    @endif
                                                                @else
                                                                    <input class="allowanceInputCheckbox"
                                                                        type="checkbox"
                                                                        id="inlineCheckboxAllowance{{ $key }}"
                                                                        checked name="allowance_id[]"
                                                                        value="{{ $allowance->allowance_id }}">
                                                                    <label
                                                                        for="inlineCheckboxAllowance{{ $key }}">{{ $allowance->allowance_name }}</label>
                                                                @endif
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endforeach

                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('paygrade.deduction')<span
                                                class="validateRq">*</span></label>
                                        <table border="1" style="border: 1px solid #ddd;" class="table">
                                            <thead class="thead-bar">
                                                <tr>
                                                    <th>
                                                        <div class="checkbox checkbox-info">
                                                            <input class="inputCheckbox checkAllDeduction"
                                                                type="checkbox" id="inlineCheckbox" checked>
                                                            <label>@lang('role.select_all')</label>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($deductions as $key => $deduction)
                                                    <tr>
                                                        <td>
                                                            @if (isset($sortedPayGradeWiseDeductionData))
                                                                <?php
                                                                $ifStoredInDeduction = array_search($deduction->deduction_id, array_column($sortedPayGradeWiseDeductionData, 'deduction_id'));
                                                                ?>
                                                                @if (gettype($ifStoredInDeduction) == 'integer')
                                                                    <div class="checkbox checkbox-info">
                                                                        <input class="deductionInputCheckbox"
                                                                            type="checkbox"
                                                                            id="inlineCheckboxDeductions{{ $key }}"
                                                                            checked name="deduction_id[]"
                                                                            value="{{ $deduction->deduction_id }}">
                                                                        <label
                                                                            for="inlineCheckboxDeductions{{ $key }}">{{ $deduction->deduction_name }}</label>
                                                                    </div>
                                                                @else
                                                                    <div class="checkbox checkbox-info">
                                                                        <input class="deductionInputCheckbox"
                                                                            type="checkbox"
                                                                            id="inlineCheckboxDeductions{{ $key }}"
                                                                            name="deduction_id[]"
                                                                            value="{{ $deduction->deduction_id }}">
                                                                        <label
                                                                            for="inlineCheckboxDeductions{{ $key }}">{{ $deduction->deduction_name }}</label>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="checkbox checkbox-info">
                                                                    <input class="deductionInputCheckbox"
                                                                        type="checkbox"
                                                                        id="inlineCheckboxDeductions{{ $key }}"
                                                                        checked name="deduction_id[]"
                                                                        value="{{ $deduction->deduction_id }}">
                                                                    <label
                                                                        for="inlineCheckboxDeductions{{ $key }}">{{ $deduction->deduction_name }}</label>
                                                                </div>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('paygrade.over_time_rate')
                                            (@lang('paygrade.per_hour'))</label>
                                        {!! Form::number(
                                            'overtime_rate',
                                            Input::old('overtime_rate'),
                                            $attributes = [
                                                'class' => 'form-control overtime_rate',
                                                'id' => 'overtime_rate',
                                                'placeholder' => __('paygrade.over_time_rate'),
                                                'min' => '0',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <input type="button"
                                                class="form-control btn btn-danger deletePayGrade appendBtnColor paygradeDltBtn hidden"
                                                style="margin-top: 17px" value="@lang('common.delete')">
                                        </div>
                                    </div>


                                    <hr>

                                </div>



                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (isset($editModeData))
                                        <button type="submit" class="btn btn-info btn_style"><i
                                                class="fa fa-pencil"></i> @lang('common.update')</button>
                                    @else
                                        <button type="submit" class="btn btn-info btn_style"><i
                                                class="fa fa-check"></i> @lang('common.save')</button>
                                    @endif
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
<div class="row_element4" style="display: none;">

    <input name="employeeBankDetails_bid[]" type="hidden">

    <div class="row">

        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.account_number')</label>

                <input class="form-control account_number" id="account_number" placeholder="@lang('employee.account_number')"
                    name="account_number" type="text" value="{{ old('account_number') }}">

            </div>

        </div>
        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.ifsc_number')</label>

                <input class="form-control ifsc_number" id="ifsc_number" placeholder="@lang('employee.ifsc_number')"
                    name="ifsc_number" type="text" value="{{ old('ifsc_number') }}">

            </div>

        </div>
        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.name_of_bank')</label>

                <input class="form-control name_of_bank" id="name_of_bank" placeholder="@lang('employee.name_of_bank')"
                    name="name_of_bank" type="text" value="{{ old('name_of_bank') }}">

            </div>

        </div>


        <div class="col-md-3">

            <div class="form-group">

                <label for="exampleInput">@lang('employee.account_holder')</label>

                <input class="form-control account_holder" id="account_holder" placeholder="@lang('employee.account_holder')"
                    name="account_holder" type="text" value="{{ old('account_holder') }}">

            </div>

        </div>



    </div>



    <div class="row">

        <div class="col-md-3"></div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="button" class="form-control btn btn-danger deletebankDetails appendBtnColor"
                    style="margin-top: 17px" value="@lang('common.delete')">
            </div>
        </div>



    </div>



    <hr>

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
        $('#addPayGrade').click(function() {

            $('.paygrade_append_div').append('<div class="paygrade_row_element">' + $('.row_element3')
                .html() + '</div>');
            $('.paygradeDltBtn').removeClass('hidden');
            $('.addPayGrade').addClass('hidden');
        });
        $('#addbankdetails').click(function() {

            $('.bank_append_div').append('<div class="bank_row_element">' + $('.row_element4')
                .html() + '</div>');
        });
        $('#emergencyContactInfo').click(function() {

            $('.emergency_append_div').append('<div class="emergency_row_element">' + $('.row_element5')
                .html() + '</div>');

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

        $(document).on("click", ".deletePayGrade", function() {
            $('.paygradeDltBtn').addClass('hidden');
            $('.addPayGrade').removeClass('hidden');
            $(this).parents('.paygrade_row_element').remove();

            var deletedID = $(this).parents('.paygrade_row_element').find('.pay_grade_id')
                .val();

            if (deletedID) {
                alert(deletedID);
                var prevDelId = $('#pay_grade_id').val();

                if (prevDelId) {

                    $('#pay_grade_id').val(prevDelId + ',' + deletedID);

                } else {

                    $('#pay_grade_id').val(deletedID);

                }

            }

        });

        $(document).on("click", ".deletebankDetails", function() {

            $(this).parents('.bank_row_element').remove();

            var deletedID = $(this).parents('.bank_row_element').find('.bank_details_id')
                .val();

            if (deletedID) {

                var prevDelId = $('#bank_details_id').val();

                if (prevDelId) {

                    $('#bank_details_id').val(prevDelId + ',' + deletedID);

                } else {

                    $('#bank_details_id').val(deletedID);

                }

            }

        });
        $(document).on("click", ".deleteEmergencyInfo", function() {

            $(this).parents('.emergency_row_element').remove();

            var deletedID = $(this).parents('.emergency_row_element').find('.emergency_info_id')
                .val();

            if (deletedID) {

                var prevDelId = $('#emergency_info_id').val();

                if (prevDelId) {

                    $('#emergency_info_id').val(prevDelId + ',' + deletedID);

                } else {

                    $('#emergency_info_id').val(deletedID);

                }

            }

        });

    });

    jQuery(function() {

        $("#payGradeForm").validate();

        $(document).on("change",
            ".gross_salary,.professional_tax,.deduction_of_epf,.deduction_of_esic,.net_pay,.ctc,.medical_insurance,.monthly_ctc",
            function() {
                var gross_salary = $('.gross_salary').val();
                var percentage_of_basic = $('.percentage_of_basic').val();
                var basicSalary = 0;
                var otherAllowance = 0;
                var allowance = 0;
                var deduction = 0;
                basicSalary = (gross_salary * percentage_of_basic) / 100;
                $('.basic_salary').val(basicSalary);
                hra = (basicSalary * percentage_of_basic) / 100;
                $('.hra').val(hra);

                var gross_salary = parseFloat($('.gross_salary').val());
                var basic_salary = parseFloat($('.basic_salary').val());
                var hra = parseFloat($('.hra').val());

                if (gross_salary <= 15000) {
                    var deduction_of_epf = parseFloat($('.deduction_of_epf').val((0.12 *
                        gross_salary)));
                    var deduction_of_esic = parseFloat($('.deduction_of_esic').val((0.0075 *
                        gross_salary)));
                    var professional_tax = parseFloat($('.professional_tax').val(208));
                    var employer_esic = parseFloat($('.employer_esic').val((3.25 / 100) * gross_salary));

                } else if (gross_salary <= 21000) {
                    var deduction_of_epf = parseFloat($('.deduction_of_epf').val(1800));
                    var deduction_of_esic = parseFloat($('.deduction_of_esic').val((0.0075 *
                        gross_salary)));
                    var professional_tax = parseFloat($('.professional_tax').val(208));
                    var employer_esic = parseFloat($('.employer_esic').val((3.25 / 100) * gross_salary));

                } else if (gross_salary >= 21000) {
                    var deduction_of_epf = parseFloat($('.deduction_of_epf').val(1800));
                    var deduction_of_esic = parseFloat($('.deduction_of_esic').val(0));
                    var professional_tax = parseFloat($('.professional_tax').val(208));
                    var employer_esic = parseFloat($('.employer_esic').val(0));

                }

                // var conveyance = parseFloat($('.conveyance').val());
                // var shift_allowance = parseFloat($('.shift_allowance').val());
                // var incentive = parseFloat($('.incentive').val());
                var variable_pay = parseFloat($('.variable_pay').val());
                // var medical_allowance = parseFloat($('.medical_allowance').val());
                var medical_insurance = parseFloat($('.medical_insurance').val());
                var deduction_of_epf = parseFloat($('.deduction_of_epf').val());
                var deduction_of_esic = parseFloat($('.deduction_of_esic').val());
                var professional_tax = parseFloat($('.professional_tax').val());
                var employer_esic = parseFloat($('.employer_esic').val());

                // allowance = (basic_salary + +conveyance + +medical_allowance + +
                //     shift_allowance + +hra +
                //     +incentive);

                // var otherAllowance = gross_salary - allowance;
                // $('.other_allowance').val(otherAllowance);
                // $('.total_allowance').val((otherAllowance + +allowance));

                // var other_allowance = parseFloat($('.other_allowance').val());

                // var total_allowance = parseFloat($('.total_allowance').val());

                // if (total_allowance > gross_salary) {
                //     alert("Enterd allowance values exceeds the gross total value!");
                // }

                deduction = (professional_tax + +deduction_of_epf + +deduction_of_esic);


                $('.total_deduction').val(deduction);

                var total_deduction = parseFloat($('.total_deduction').val());

                $('.net_pay').val((gross_salary - total_deduction));

                var net_pay = parseFloat($('.net_pay').val());

                $('.monthly_ctc').val(((total_deduction + +employer_esic + +deduction_of_epf + +net_pay +
                    +medical_insurance + +variable_pay)));





                $('.ctc').val(((total_deduction + +employer_esic + +deduction_of_epf + +net_pay +
                    +medical_insurance + +variable_pay) * 12));


                var employer_esic = parseFloat($('.employer_esic').val());
                var basic_salary = parseFloat($('.basic_salary').val());
                var hra = parseFloat($('.hra').val());
                var monthly_ctc = parseFloat($('.monthly_ctc').val());
                var ctc = parseFloat($('.ctc').val());

                // setInterval(() => {
                //     alert(ctc);
                // }, 3000);

                $('.hra').val(hra.toFixed(2));
                $('.basic_salary').val(basic_salary.toFixed(2));
                $('.employer_esic').val(employer_esic.toFixed(2));
                $('.net_pay').val(net_pay.toFixed(2));
                $('.monthly_ctc').val(monthly_ctc.toFixed(2));
                $('.ctc').val(ctc.toFixed(2));
                $('.other_allowance').val(other_allowance.toFixed(2));
                $('.deduction_of_epf').val(deduction_of_epf.toFixed(2));
                $('.deduction_of_esic').val(deduction_of_esic.toFixed(2));
                $('.shift_allowance').val(shift_allowance.toFixed(2));
                $('.incentive').val(incentive.toFixed(2));
                $('.variable_pay').val(variable_pay.toFixed(2));
                $('.medical_allowance').val(medical_allowance.toFixed(2));
                $('.conveyance').val(conveyance.toFixed(2));
                $('.medical_insurance').val(medical_insurance.toFixed(2));
                $('.professional_tax').val(professional_tax.toFixed(2));


            });

        $(document).on("change",
            ".conveyance,.medical_allowance,.shift_allowance,.incentive,.variable_pay",
            function() {

                var gross_salary = parseFloat($('.gross_salary').val());
                var basic_salary = parseFloat($('.basic_salary').val());
                var hra = parseFloat($('.hra').val());
                var conveyance = parseFloat($('.conveyance').val());
                var medical_allowance = parseFloat($('.medical_allowance').val());
                var shift_allowance = parseFloat($('.shift_allowance').val());
                var incentive = parseFloat($('.incentive').val());
                var variable_pay = parseFloat($('.variable_pay').val());

                var allowance = 0;


                allowance = (basic_salary + +conveyance + +medical_allowance + +shift_allowance + +hra +
                    +incentive + +variable_pay);
                var otherAllowance = gross_salary - allowance;
                $('.other_allowance').val(otherAllowance.toFixed(2));
                $('.total_allowance').val((otherAllowance + +allowance));

                var other_allowance = parseFloat($('.other_allowance').val());

                if (other_allowance < 0) {
                    alert("Enter allowance values exceeds the gross total value!");
                }

            });

        // $(document).on("change",
        //     ".gross_salary,.professional_tax,.deduction_of_epf,.deduction_of_esic,.net_pay,.ctc",
        //     function() {


        //         var deduction = 0;
        //         var gross_salary = parseFloat($('.gross_salary').val());
        //         var basic_salary = parseFloat($('.basic_salary').val());

        //         if (gross_salary >= 21000 && basic_salary >= 15000) {
        //             // $('.deduction_of_esic').addClass('readonly-text');
        //             // $('.deduction_of_epf').addClass('readonly-text');
        //             var deduction_of_esic = parseFloat($('.deduction_of_epf').val(1800));
        //             var deduction_of_epf = parseFloat($('.deduction_of_esic').val(0));
        //         } else if (gross_salary <= 21000 && basic_salary <= 15000) {
        //             // $('.deduction_of_epf').removeClass('readonly-text');
        //             // $('.deduction_of_esic').removeClass('readonly-text');
        //             var deduction_of_epf = parseFloat($('.deduction_of_epf').val((0.12 * gross_salary)));
        //             var deduction_of_esic = parseFloat($('.deduction_of_esic').val((0.75 * gross_salary)));
        //         }


        //         var professional_tax = parseFloat($('.professional_tax').val(208));

        //         deduction = (professional_tax + +deduction_of_epf + +deduction_of_esic);

        //         $('.total_deduction').val(deduction.toFixed(2));
        //         var total_deduction = parseFloat($('.total_deduction').val());
        //         var total_allowance = parseFloat($('.total_allowance').val());

        //         var net_pay = parseFloat($('.net_pay').val(total_allowance - total_deduction).toFixed(2));
        // });

        // $(document).on("change",
        //     ".gross_salary,.professional_tax,.deduction_of_epf,.deduction_of_esic,.net_pay,.ctc,.medical_insurance,.monthly_ctc",
        //     function() {
        //         var total_deduction = parseFloat($('.total_deduction').val());
        //         var deduction_of_esic = parseFloat($('.deduction_of_esic').val());
        //         var deduction_of_epf = parseFloat($('.deduction_of_epf').val());
        //         var medical_insurance = parseFloat($('.medical_insurance').val());


        //         var net_pay = parseFloat($('.net_pay').val());
        //         $('.monthly_ctc').val(((total_deduction + +deduction_of_esic + +deduction_of_epf + +
        //             net_pay +
        //             +medical_insurance + +variable_pay)));

        //         $('.ctc').val(((total_deduction + +deduction_of_esic + +deduction_of_epf + +net_pay + +
        //             medical_insurance) * 12).toFixed(2));
        //     });


    });
</script>
@endsection
