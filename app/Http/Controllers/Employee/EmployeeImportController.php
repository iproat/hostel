<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use App\Model\Employee;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repositories\EmployeeRepository;
use Maatwebsite\Excel\Facades\Excel as Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeImportController extends Controller
{
    protected $_employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function import(Request $request)
    {
        try {
            \DB::beginTransaction();
            $file = $request->file('select_file');
            if ($request->file('select_file')) {
                Excel::import(new EmployeeImport($request->all()), $file);
                DB::commit();
            } else {
                return back()->with('error', 'No Data Found!, Please Check the File');
            }
        } catch (ValidationException $e) {
            $import = new EmployeeImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Employee information imported successfully.');
    }

    public function import_main_fn(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:csv,xls,xlsx',
        ]);

        $bug = null;
        if ($request->file('select_file')) {
            $duplicateId           = [];
            $duplicateEmployeeName = [];
            $path                  = $request->file('select_file')->getRealPath();
            $data                  = Excel::load($path)->get();
            $date                  = \Carbon\Carbon::now();
            $lastMonth             = $date->subMonth()->format('y-m');
            foreach ($data as $key => $value) {
                // dd($data);
                $sl_no              = $value->sl_no;
                $finger_id          = $value->finger_id;
                $department_id      = DB::table('department')->where('department_name', $value->department_name)->select('department_id')->first();
                $designation_id     = DB::table('designation')->where('designation_name', $value->designation_name)->select('designation_id')->first();
                $branch_id          = DB::table('branch')->where('branch_name', $value->branch_name)->select('branch_id')->first();
                $supervisor_id      = DB::table('employee')->join('user', 'user.user_id', '=', 'employee.user_id')->where('user.user_name', $value->supervisor_name)->select('employee.supervisor_id')->first();
                $work_shift_id      = DB::table('work_shift')->where('shift_name', $value->work_shift_name)->select('work_shift_id')->first();
                $esi_card_number    = $value->esi_card_number;
                $pf_account_number  = $value->pf_account_number;
                $pay_grade_id       = $value->pay_grade_name != '' ? DB::table('pay_grade')->where('pay_grade_name', $value->pay_grade_name)->select('pay_grade_id')->first() : \null;
                $hourly_salaries_id = $value->hourly_grade_name != '' ? DB::table('hourly_salaries')->where('hourly_grade', $value->hourly_grade_name)->select('hourly_salaries_id')->first() : \null;
                $email              = $value->email;
                $phone              = $value->phone;
                // dd((int)($phone));
                $first_name         = $value->first_name;
                $last_name          = $value->last_name;
                $date_of_birth      = $value->date_of_birth;
                $date_of_joining    = $value->date_of_joining;
                $gender             = $value->gender;
                $religion           = $value->religion;
                $marital_status     = $value->marital_status;
                $address            = $value->address;
                $emergency_contacts = $value->emergency_contacts;
                // $role_id            = DB::table('role')->where('role_name', $value->role_name)->select('role_id')->first();
                $user_name = $value->user_name;
                $password  = '123';

                $duplicateEmployee     = DB::table('employee')->where('employee_id', $value->employee_id)->select('employee_id')->first();
                $duplicateEmployeeName = DB::table('employee')->where('employee_id', $value->employee_id)->select('first_name', 'last_name')->first();
                $duplicateFingerId     = DB::table('employee')->where('finger_id', $value->finger_id)->select('finger_id')->first();
                $user_id               = DB::table('user')->orderBy('user_id', 'desc')->select('user_id')->first();
                $duplicateUserName     = DB::table('user')->where('user_name', $value->user_name)->select('user_name')->first();
                // dd($duplicateUserName);
                // dd($duplicateFingerId);
                // dd(($user_id->user_id));
                if (
                    !$duplicateEmployee
                    && !$duplicateEmployeeName && !$duplicateFingerId && !$duplicateUserName
                    && isset($finger_id) && isset($role_id) && isset($user_name) && isset($first_name) && isset($department_id)
                    && isset($designation_id) && isset($work_shift_id) && (isset($pay_grade_id) || isset($hourly_salaries_id)) && isset($gender)
                    && isset($date_of_birth) && isset($date_of_joining)
                ) {

                    $user_list = [
                        'role_id'    => 3,
                        // 'role_id'    => $role_id->role_id,
                        'user_name'  => $user_name,
                        'password'   => Hash::make($password),
                        'created_by' => 1,
                        'updated_by' => 1,
                        'status'     => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    $employee_list = [
                        'user_id'            => isset($user_id->user_id) ? ($user_id->user_id) + 1 : 1,
                        'finger_id'          => $finger_id,
                        'department_id'      => $department_id->department_id,
                        'designation_id'     => $designation_id->designation_id,
                        'branch_id'          => $branch_id->branch_id,
                        'supervisor_id'      => $supervisor_id->supervisor_id,
                        'esi_card_number'    => (int) $esi_card_number,
                        'work_shift_id'      => $work_shift_id->work_shift_id,
                        'pf_account_number'  => (int) $pf_account_number,
                        'pay_grade_id'       => $value->pay_grade_name != '' ? $pay_grade_id->pay_grade_id : \null,
                        'hourly_salaries_id' => $value->hourly_grade_name != '' ? $hourly_salaries_id->hourly_salaries_id : \null,
                        'phone'              => (int) ($phone),
                        'email'              => $email,
                        'first_name'         => $first_name,
                        'last_name'          => $last_name,
                        'date_of_birth'      => dateConvertFormtoDB($date_of_birth),
                        'date_of_joining'    => dateConvertFormtoDB($date_of_joining),
                        'gender'             => $gender,
                        'religion'           => $religion,
                        'marital_status'     => $marital_status,
                        'address'            => $address,
                        'emergency_contacts' => (int) ($emergency_contacts),
                        'created_by'         => 1,
                        'updated_by'         => 1,
                        'status'             => 1,
                        'created_at'         => Carbon::now(),
                        'updated_at'         => Carbon::now(),
                    ];

                    // dd($user_list, $employee_list);

                    DB::beginTransaction();
                    DB::table('user')->insert($user_list);
                    // dd($user_id);
                    DB::table('employee')->insert([$employee_list]);
                    DB::commit();
                    $bug = 0;
                } elseif (isset($duplicateEmployeeName->first_name) || isset($duplicateFingerId->finger_id) || isset($duplicateEmployee->employee_id)) {
                    $duplicate_first_name  = isset($duplicateEmployeeName->first_name) ? 'Duplicate entries found for an employee name - ' . $duplicateEmployeeName->first_name . ' ' . $duplicateEmployeeName->last_name : '';
                    $duplicate_finger_id   = isset($duplicateFingerId->finger_id) ? 'Duplicate entries found for an employee finger id - ' . $duplicateFingerId->finger_id : '';
                    $duplicate_employee_id = isset($duplicateEmployee->employee_id) ? 'Duplicate entries found for an employee finger id - ' . $duplicateEmployee->employee_id : '';
                    // dd($duplicate_first_name, $duplicate_finger_id, $duplicate_employee_id);
                    return \back()->with('error', $duplicate_first_name . ' ' . $duplicate_finger_id . ' ' . $duplicate_employee_id);
                } elseif (isset($duplicateUserName->user_name)) {
                    DB::rollback();
                    return \back()->with('error', isset($duplicateUserName->user_name) ? 'Duplicate entries found for an user name - ' . $duplicateUserName->user_name : '');
                } else {
                    DB::rollback();
                    return back()->with('error', 'Cell-Heading Not Found in sheet!, Please Check the File');
                }
            }

            // print_r($value);
            // print_r($attendance_list);
            // print_r($fp);
            // print_r($time);

            // if (!empty($employee_list) && !empty($user_list)) {
            //     try {
            //         DB::beginTransaction();
            //         DB::table('user')->insert($user_list);
            //         // dd($user_id);
            //         DB::table('employee')->insert([$employee_list]);
            //         // \Session::flash('success', 'File improted successfully.');
            //         DB::commit();
            //         return back()->with('success', 'Employee salary information imported successfully.');
            //     } catch (\Exception $e) {
            //         DB::rollback();
            //         $e->getMessage();
            //         return back()->with('error', 'Something Went Wrong!, Please try Again.');
            //     }
            // }

            if ($bug == 0) {
                return back()->with('success', 'Employee salary information imported successfully.');
            } else {
                DB::rollback();
                return back()->with('error', 'Something Went Wrong!, Please try Again.');
            }
        } else {
            DB::rollback();
            return back()->with('error', 'No Data Found!, Please Check the File');
        }
    }

    public function import2(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:csv,xls,xlsx',
        ]);
        $bug = null;
        if ($request->file('select_file')) {
            $duplicateId           = [];
            $duplicateEmployeeName = [];
            $path                  = $request->file('select_file')->getRealPath();
            $data                  = Excel::load($path)->get();
            $date                  = \Carbon\Carbon::now();
            $lastMonth             = $date->subMonth()->format('y-m');
            foreach ($data as $key => $value) {
                // dd($data);
                $finger_id      = $value->finger_id;
                $department_id  = DB::table('department')->where('department_name', $value->department_name)->select('department_id')->first();
                $designation_id = DB::table('designation')->where('designation_name', $value->designation_name)->select('designation_id')->first();
                $gender         = $value->gender;
                $user_name      = $value->user_name;
                $password       = '123';

                $duplicateEmployee = DB::table('employee')->where('employee_id', $value->employee_id)->select('employee_id')->first();
                $duplicateFingerId = DB::table('employee')->where('finger_id', $value->finger_id)->select('finger_id')->first();
                $user_id           = DB::table('user')->orderBy('user_id', 'desc')->select('user_id')->first();
                $duplicateUserName = DB::table('user')->where('user_name', $value->user_name)->select('user_name')->first();

                if (!$duplicateEmployee) {

                    $user_list = [
                        'role_id'    => 5,
                        'user_name'  => $user_name,
                        'password'   => Hash::make($password),
                        'created_by' => 1,
                        'updated_by' => 1,
                        'status'     => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    $employee_list = [
                        'user_id'        => isset($user_id->user_id) ? ($user_id->user_id) + 1 : 1,
                        'finger_id'      => $finger_id,
                        'department_id'  => 1,
                        'designation_id' => 1,
                        'first_name'     => $user_name,
                        'gender'         => $gender,
                        'created_by'     => 1,
                        'updated_by'     => 1,
                        'status'         => 1,
                        'created_at'     => Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ];

                    // dd($user_list, $employee_list);

                    DB::beginTransaction();
                    DB::table('user')->insert($user_list);
                    // dd($user_id);
                    DB::table('employee')->insert([$employee_list]);
                    DB::commit();
                    $bug = 0;
                } elseif (isset($duplicateUserName->user_name)) {
                    DB::rollback();
                    return \back()->with('error', isset($duplicateUserName->user_name) ? 'Duplicate entries found for an user name - ' . $duplicateUserName->user_name : '');
                } else {
                    DB::rollback();
                    return back()->with('error', 'Cell-Heading Not Found in sheet!, Please Check the File');
                }
            }

            if ($bug == 0) {
                return back()->with('success', 'Employee salary information imported successfully.');
            } else {
                DB::rollback();
                return back()->with('error', 'Something Went Wrong!, Please try Again.');
            }
        } else {
            DB::rollback();
            return back()->with('error', 'No Data Found!, Please Check the File');
        }
    }

    public function import_df(Request $request)
    {
        $validate_image = $request->validate([
            'select_file' => 'required|mimes:csv,xls,xlsx',
        ]);

        if ($validate_image) {
            // EXCEL V2.1 SUPPORTED QUERY
            $path = $request->file('select_file')->getRealPath();
            $data = Excel::load($path)->get();
            foreach ($data->toArray() as $row) {
                // dd($row);
                $bug  = \null;
                $time = Carbon::now();
                try {
                    DB::beginTransaction();
                    $user_id = DB::table('user')->insertGetId([
                        'user_name'  => $row['first_name'],
                        'role_id'    => 3,
                        'created_by' => 1,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);
                    Employee::firstOrCreate([
                        'user_id'         => $user_id,
                        'finger_id'       => $row['finger_id'],
                        'first_name'      => $row['first_name'],
                        'department_id'   => 1,
                        'designation_id'  => 2,
                        'work_shift_id'   => 1,
                        'pay_grade_id'    => 1,
                        'supervisor_id'   => 1,
                        'gender'          => "None",
                        'branch_id'       => 1,
                        'created_by'      => 1,
                        'updated_by'      => 1,
                        'date_of_joining' => $time,
                        'date_of_birth'   => $time,
                    ]);
                    DB::commit();
                    $bug = 0;
                } catch (\Throwable $e) {
                    DB::rollback();
                    $bug = 1;
                    return redirect('employee')->with('error', 'Something Went Wrong!, Please try Again.');
                }
            }
        } else {
            return redirect('employee')->with('error', $validate_image->errors());
        }

        if ($bug == 0) {
            return redirect('employee')->with('success', 'Employee information has been imported successfully.');
        } else {
            DB::rollback();
            return redirect('employee')->with('error', 'Something Went Wrong!, Please try Again.');
        }
    }

    public function import6(Request $request)
    {
        $path = $request->file('select_file')->getRealPath();
        $data = Excel::load($path)->get();
        $bug  = \null;
        Excel::load(Input::file('select_file'), function ($reader) {
            foreach ($reader->toArray() as $row) {
                // dd($row);
                $time      = Carbon::now();
                $duplicate = Employee::where('finger_id', $row['finger_id'])->first();
                if (!$duplicate) {
                    DB::beginTransaction();
                    $user_id = DB::table('user')->insertGetId([
                        'user_name'  => $row['first_name'],
                        'role_id'    => 3,
                        'created_by' => 1,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);
                    Employee::firstOrCreate([
                        'user_id'         => $user_id,
                        'finger_id'       => $row['finger_id'],
                        'first_name'      => $row['first_name'],
                        'department_id'   => 1,
                        'designation_id'  => 2,
                        'work_shift_id'   => 1,
                        'pay_grade_id'    => 1,
                        'supervisor_id'   => 1,
                        'gender'          => "None",
                        'branch_id'       => 1,
                        'created_by'      => 1,
                        'updated_by'      => 1,
                        'date_of_joining' => $time,
                        'date_of_birth'   => $time,
                    ]);
                    DB::commit();
                    $bug = 0;
                }
            }
        });
        dd($bug);
        if ($bug == 0) {
            return redirect('employee')->with('success', 'Employee information has been imported successfully.');
        } else {
            DB::rollback();
            return redirect('employee')->with('error', 'Something Went Wrong!, Please try Again.');
        }
    }

    public function import5(Request $request)
    {

        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx',
        ]);
        if ($request->file('select_file')) {

            $path        = $request->file('select_file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            // dd($sheet->toArray());
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(2, $row_limit);
            $column_range = range('W', $column_limit);
            $startcount   = 2;
            $data         = array();
            $user_id      = DB::table('user')->orderBy('user_id', 'desc')->select('user_id')->first();
            // dd($user_id);
            try {
                foreach ($row_range as $key => $row) {
                    $employee_id     = $sheet->getCell('A' . $row)->getValue();
                    $employee_name   = $sheet->getCell('B' . $row)->getValue();
                    $month_of_salary = $sheet->getCell('C' . $row)->getValue();
                    $duplicateId     = DB::table('employee')->where('employee_id', $employee_id)->first();
                    if (!$duplicateId) {
                        // dd($row, $row_range, $row_limit, $column_limit, $sheet->getCell('A' . $row)->getValue(),);
                        $dataUsers[] = [
                            'role_id'    => 5,
                            'user_name'  => $sheet->getCell('B' . $row)->getValue(),
                            'password'   => Hash::make(12345),
                            'created_by' => 1,
                            'updated_by' => 1,
                            'status'     => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];

                        $data[] = [
                            'finger_id'      => $sheet->getCell('A' . $row)->getValue(),
                            'first_name'     => $sheet->getCell('B' . $row)->getValue(),
                            'designation_id' => $sheet->getCell('C' . $row)->getValue(),
                            'user_id'        => isset($user_id->user_id) ? (($user_id->user_id) + $key + 1) : 1,
                            'department_id'  => 1,
                            'created_by'     => 1,
                            'updated_by'     => 1,
                            'created_at'     => Carbon::now(),
                            'updated_at'     => Carbon::now(),
                        ];
                        $startcount++;
                    }
                    //  dd($data,$dataUsers);

                }
                DB::beginTransaction();
                DB::table('user')->insert($dataUsers);
                DB::table('employee')->insert($data);
                DB::commit();
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                DB::rollback();
                return back()->with('error', 'There was a problem uploading the data!');
            }
        } else {
            return back()->with('error', 'Select the file!');
        }

        return back()->with('success', 'Great! Data has been successfully uploaded.');
    }
}
