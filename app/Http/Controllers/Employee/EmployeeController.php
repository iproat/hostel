<?php

namespace App\Http\Controllers\Employee;

use App\User;
use DateTime;
use Carbon\Carbon;

use App\Model\Role;

use App\Model\Branch;

use App\Model\Photos;

use App\Model\Employee;

use App\Model\PayGrade;

use App\Model\Allowance;

use App\Model\Deduction;

use App\Model\WorkShift;

use App\Model\Department;


use App\Model\Designation;

use App\Model\HourlySalary;

use Illuminate\Http\Request;

use Illuminate\Http\Response;



use App\Model\EmployeePayGrade;
use App\Model\EmployeeExperience;
use App\Model\PayGradeToAllowance;
use App\Model\PayGradeToDeduction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeDetailsExport;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PayGradeRequest;
use Illuminate\Support\Facades\Storage;
use App\Repositories\EmployeeRepository;
use App\Model\EmployeeEducationQualification;


class EmployeeController extends Controller
{

    protected $employeeRepositories;

    public function __construct(EmployeeRepository $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }


    public function index(Request $request)
    {
        $departmentList     = Department::get();
        $designationList    = Designation::get();
        $roleList           = Role::get();


        $results = Employee::with(['userName' => function ($q) {
            $q->with('role');
        }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])
            ->orderBy('employee_id', 'DESC')->paginate(10);

        if (request()->ajax()) {

            if ($request->role_id != '') {
                $results = Employee::whereHas('userName', function ($q) use ($request) {
                    $q->with('role')->where('role_id', $request->role_id);
                })->with('department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries')->orderBy('employee_id', 'DESC');
            } else {
                $results = Employee::with(['userName' => function ($q) {
                    $q->with('role');
                }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])->orderBy('employee_id', 'DESC');
            }

            if ($request->department_id != '') {
                $results->where('department_id', $request->department_id);
            }

            if ($request->designation_id != '') {
                $results->where('designation_id', $request->designation_id);
            }

            if ($request->employee_name != '') {
                $results->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }



            $results = $results->paginate(10);
            return   View('admin.employee.employee.pagination', ['results' => $results])->render();
        }

        return view('admin.employee.employee.index', ['results' => $results, 'departmentList' => $departmentList, 'designationList' => $designationList, 'roleList' => $roleList]);
    }


    public function create()
    {
        $userList           = User::where('status', 1)->get();
        $roleList           = Role::get();
        $departmentList     = Department::get();
        $designationList    = Designation::get();
        $branchList         = Branch::get();
        $workShiftList      = WorkShift::get();
        $supervisorList     = Employee::where('status', 1)->get();
        $payGradeList       = PayGrade::all();
        $hourlyPayGradeList = HourlySalary::all();
        $allowances         = Allowance::all();
        $deductions         = Deduction::all();


        $data = [
            'userList'          => $userList,
            'roleList'          => $roleList,
            'departmentList'    => $departmentList,
            'designationList'   => $designationList,
            'branchList'        => $branchList,
            'supervisorList'    => $supervisorList,
            'workShiftList'     => $workShiftList,
            'payGradeList'      => $payGradeList,
            'hourlyPayGradeList' => $hourlyPayGradeList,
            'allowances'      => $allowances,
            'deductions' => $deductions,

        ];

        return view('admin.employee.employee.addEmployee', $data);
    }


    public function store(EmployeeRequest $request)
    {
        dd($request->all());
        $photo = $request->file('photo');
        $document = $request->file('document_file');
        $document2 = $request->file('document_file2');
        $document3 = $request->file('document_file3');
        $document4 = $request->file('document_file4');
        $document5 = $request->file('document_file5');
        $document6 = $request->file('document_file6');
        $document7 = $request->file('document_file7');
        $document8 = $request->file('document_file8');
        $document9 = $request->file('document_file9');
        $document10 = $request->file('document_file10');
        $document11 = $request->file('document_file11');
        $document12 = $request->file('document_file12');
        $document13 = $request->file('document_file13');
        $document14 = $request->file('document_file14');
        $document15 = $request->file('document_file15');
        $document16 = $request->file('document_file16');
        $document17 = $request->file('document_file17');
        $document18 = $request->file('document_file18');
        $document19 = $request->file('document_file19');
        $document20 = $request->file('document_file20');
        $document21 = $request->file('document_file21');

        if ($photo) {
            $imgName = md5(str_random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('uploads/employeePhoto/', $imgName);
            $employeePhoto['photo'] = $imgName;
        }
        if ($document) {
            $document_name = date('Y_m_d_H_i_s') . '_' . $request->file('document_file')->getClientOriginalName();
            $request->file('document_file')->move('uploads/employeeDocuments/', $document_name);
            $employeeDocument['document_file'] = $document_name;
        }
        if ($document2) {
            $document_name2 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file2')->getClientOriginalName();
            $request->file('document_file2')->move('uploads/employeeDocuments/', $document_name2);
            $employeeDocument['document_file2'] = $document_name2;
        }
        if ($document3) {
            $document_name3 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file3')->getClientOriginalName();
            $request->file('document_file3')->move('uploads/employeeDocuments/', $document_name3);
            $employeeDocument['document_file3'] = $document_name3;
        }
        if ($document4) {
            $document_name4 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file4')->getClientOriginalName();
            $request->file('document_file4')->move('uploads/employeeDocuments/', $document_name4);
            $employeeDocument['document_file4'] = $document_name4;
        }
        if ($document5) {
            $document_name5 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file5')->getClientOriginalName();
            $request->file('document_file5')->move('uploads/employeeDocuments/', $document_name5);
            $employeeDocument['document_file5'] = $document_name5;
        }
        if ($document6) {
            $document_name6 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file6')->getClientOriginalName();
            $request->file('document_file6')->move('uploads/employeeDocuments/', $document_name6);
            $employeeDocument['document_file6'] = $document_name6;
        }
        if ($document7) {
            $document_name7 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file7')->getClientOriginalName();
            $request->file('document_file7')->move('uploads/employeeDocuments/', $document_name7);
            $employeeDocument['document_file7'] = $document_name7;
        }
        if ($document8) {
            $document_name8 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file8')->getClientOriginalName();
            $request->file('document_file8')->move('uploads/employeeDocuments/', $document_name8);
            $employeeDocument['document_file8'] = $document_name8;
        }
        if ($document9) {
            $document_name9 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file9')->getClientOriginalName();
            $request->file('document_file9')->move('uploads/employeeDocuments/', $document_name9);
            $employeeDocument['document_file9'] = $document_name9;
        }
        if ($document10) {
            $document_name10 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file10')->getClientOriginalName();
            $request->file('document_file10')->move('uploads/employeeDocuments/', $document_name10);
            $employeeDocument['document_file10'] = $document_name10;
        }
        if ($document11) {
            $document_name11 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file11')->getClientOriginalName();
            $request->file('document_file11')->move('uploads/employeeDocuments/', $document_name11);
            $employeeDocument['document_file11'] = $document_name11;
        }

        if ($document12) {
            $document_name12 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file12')->getClientOriginalName();
            $request->file('document_file12')->move('uploads/employeeDocuments/', $document_name12);
            $employeeDocument['document_file12'] = $document_name12;
        }

        if ($document13) {
            $document_name13 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file13')->getClientOriginalName();
            $request->file('document_file13')->move('uploads/employeeDocuments/', $document_name13);
            $employeeDocument['document_file13'] = $document_name13;
        }

        if ($document14) {
            $document_name14 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file14')->getClientOriginalName();
            $request->file('document_file14')->move('uploads/employeeDocuments/', $document_name14);
            $employeeDocument['document_file14'] = $document_name14;
        }

        if ($document15) {
            $document_name15 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file15')->getClientOriginalName();
            $request->file('document_file15')->move('uploads/employeeDocuments/', $document_name15);
            $employeeDocument['document_file15'] = $document_name15;
        }

        if ($document16) {
            $document_name16 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file16')->getClientOriginalName();
            $request->file('document_file16')->move('uploads/employeeDocuments/', $document_name16);
            $employeeDocument['document_file16'] = $document_name16;
        }

        if ($document17) {
            $document_name17 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file17')->getClientOriginalName();
            $request->file('document_file17')->move('uploads/employeeDocuments/', $document_name17);
            $employeeDocument['document_file17'] = $document_name17;
        }

        if ($document18) {
            $document_name18 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file18')->getClientOriginalName();
            $request->file('document_file18')->move('uploads/employeeDocuments/', $document_name18);
            $employeeDocument['document_file18'] = $document_name18;
        }

        if ($document19) {
            $document_name19 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file19')->getClientOriginalName();
            $request->file('document_file19')->move('uploads/employeeDocuments/', $document_name19);
            $employeeDocument['document_file19'] = $document_name19;
        }

        if ($document20) {
            $document_name20 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file20')->getClientOriginalName();
            $request->file('document_file20')->move('uploads/employeeDocuments/', $document_name20);
            $employeeDocument['document_file20'] = $document_name20;
        }

        if ($document21) {
            $document_name21 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file21')->getClientOriginalName();
            $request->file('document_file21')->move('uploads/employeeDocuments/', $document_name21);
            $employeeDocument['document_file21'] = $document_name21;
        }

        $employeeDataFormat  = $this->employeeRepositories->makeEmployeeDocumentInformationDataFormat($request->all());
        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }
        try {
            DB::beginTransaction();

            $employeeAccountDataFormat  = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all());
            $parentData = User::create($employeeAccountDataFormat);

            $employeeData['user_id'] = $parentData->user_id;
            $childData = Employee::create($employeeData);

            $employeePayGradeData  = $this->employeeRepositories->makeEmployeePaygradeDataFormat($request->all(), $childData->employee_id);
            if (count($employeePayGradeData) > 0) {
                EmployeePayGrade::insert($employeePayGradeData);
            }

            $employeeBankDetailsData  = $this->employeeRepositories->makeEmployeeBankDetailsDataFormat($request->all(), $childData->employee_id);
            if (count($employeeBankDetailsData) > 0) {
                Employee::where('employee_id', $childData->employee_id)->insert($employeeBankDetailsData);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            $bug = 1;
        }
        // dd($employeeAccountDataFormat, $childData, $employeeEducationData, $employeeExperienceData, $employeePayGradeData, $employeeBankDetailsData);
        if ($bug == 0) {
            return redirect('employee')->with('success', 'Employee information successfully saved.');
        } else {
            return redirect('employee')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public static function storePayGrade(PayGradeRequest $request, $employee_id)
    {
        $input = $request->all();
        $input['employee_id'] = $employee_id;
        $allowance = [];
        $deduction = [];
        try {
            DB::beginTransaction();

            $result = PayGrade::create($input);

            if (isset($input['allowance_id'])) {
                for ($i = 0; $i < count($input['allowance_id']); $i++) {
                    $allowance[$i] = [
                        'pay_grade_id'       => $result->pay_grade_id,
                        'allowance_id'       => $input['allowance_id'][$i],
                        'created_at'         => Carbon::now(),
                        'updated_at'         => Carbon::now(),
                    ];
                }
                PayGradeToAllowance::insert($allowance);
            }

            if (isset($input['deduction_id'])) {
                for ($i = 0; $i < count($input['deduction_id']); $i++) {
                    $deduction[$i] = [
                        'pay_grade_id'       => $result->pay_grade_id,
                        'deduction_id'         => $input['deduction_id'][$i],
                        'created_at'         => Carbon::now(),
                        'updated_at'         => Carbon::now(),
                    ];
                }
                PayGradeToDeduction::insert($deduction);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id)
    {
        $userList           = User::where('status', 1)->get();
        $roleList           = Role::get();
        $departmentList     = Department::get();
        $designationList    = Designation::get();
        $branchList         = Branch::get();
        $supervisorList     = Employee::where('status', 1)->get();
        $editModeData       = Employee::findOrFail($id);
        $workShiftList      = WorkShift::get();
        $payGradeList       = PayGrade::all();
        $hourlyPayGradeList = HourlySalary::all();

        $employeeAccountEditModeData        = User::where('user_id', $editModeData->user_id)->first();
        $educationQualificationEditModeData = EmployeeEducationQualification::where('employee_id', $id)->get();
        $experienceEditModeData             = EmployeeExperience::where('employee_id', $id)->get();

        $data = [
            'userList'          => $userList,
            'roleList'          => $roleList,
            'departmentList'    => $departmentList,
            'designationList'   => $designationList,
            'branchList'        => $branchList,
            'supervisorList'    => $supervisorList,
            'workShiftList'     => $workShiftList,
            'payGradeList'      => $payGradeList,
            'editModeData'      => $editModeData,
            'hourlyPayGradeList' => $hourlyPayGradeList,
            'employeeAccountEditModeData'         => $employeeAccountEditModeData,
            'educationQualificationEditModeData'  => $educationQualificationEditModeData,
            'experienceEditModeData'              => $experienceEditModeData,
        ];

        return view('admin.employee.employee.editEmployee', $data);
    }


    public function update(EmployeeRequest $request, $id)
    {

        $document = $request->file('document_file');
        $document2 = $request->file('document_file2');
        $document3 = $request->file('document_file3');
        $document4 = $request->file('document_file4');
        $document5 = $request->file('document_file5');
        $document6 = $request->file('document_file6');
        $document7 = $request->file('document_file7');
        $document8 = $request->file('document_file8');
        $document9 = $request->file('document_file9');
        $document10 = $request->file('document_file10');
        $document11 = $request->file('document_file11');
        $document12 = $request->file('document_file12');
        $document13 = $request->file('document_file13');
        $document14 = $request->file('document_file14');
        $document15 = $request->file('document_file15');
        $document16 = $request->file('document_file16');
        $document17 = $request->file('document_file17');
        $document18 = $request->file('document_file18');
        $document19 = $request->file('document_file19');
        $document20 = $request->file('document_file20');
        $document21 = $request->file('document_file21');

        $employee = Employee::findOrFail($id);
        $document = $request->file('document_file');
        $photo = $request->file('photo');
        if ($photo) {
            $imgName = md5(str_random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('uploads/employeePhoto/', $imgName);
            if (file_exists('uploads/employeePhoto/' . $employee->photo) and !empty($employee->photo)) {
                unlink('uploads/employeePhoto/' . $employee->photo);
            }
            $employeePhoto['photo'] = $imgName;
        }
        if ($document) {
            $document_name = date('Y_m_d_H_i_s') . '_' . $request->file('document_file')->getClientOriginalName();
            $request->file('document_file')->move('uploads/employeeDocuments/', $document_name);
            $employeeDocument['document_file'] = $document_name;
        }
        if ($document2) {
            $document_name2 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file2')->getClientOriginalName();
            $request->file('document_file2')->move('uploads/employeeDocuments/', $document_name2);
            $employeeDocument['document_file2'] = $document_name2;
        }
        if ($document3) {
            $document_name3 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file3')->getClientOriginalName();
            $request->file('document_file3')->move('uploads/employeeDocuments/', $document_name3);
            $employeeDocument['document_file3'] = $document_name3;
        }
        if ($document4) {
            $document_name4 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file4')->getClientOriginalName();
            $request->file('document_file4')->move('uploads/employeeDocuments/', $document_name4);
            $employeeDocument['document_file4'] = $document_name4;
        }
        if ($document5) {
            $document_name5 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file5')->getClientOriginalName();
            $request->file('document_file5')->move('uploads/employeeDocuments/', $document_name5);
            $employeeDocument['document_file5'] = $document_name5;
        }
        if ($document6) {
            $document_name6 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file6')->getClientOriginalName();
            $request->file('document_file6')->move('uploads/employeeDocuments/', $document_name6);
            $employeeDocument['document_file6'] = $document_name6;
        }
        if ($document7) {
            $document_name7 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file7')->getClientOriginalName();
            $request->file('document_file7')->move('uploads/employeeDocuments/', $document_name7);
            $employeeDocument['document_file7'] = $document_name7;
        }
        if ($document8) {
            $document_name8 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file8')->getClientOriginalName();
            $request->file('document_file8')->move('uploads/employeeDocuments/', $document_name8);
            $employeeDocument['document_file8'] = $document_name8;
        }
        if ($document9) {
            $document_name9 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file9')->getClientOriginalName();
            $request->file('document_file9')->move('uploads/employeeDocuments/', $document_name9);
            $employeeDocument['document_file9'] = $document_name9;
        }
        if ($document10) {
            $document_name10 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file10')->getClientOriginalName();
            $request->file('document_file10')->move('uploads/employeeDocuments/', $document_name10);
            $employeeDocument['document_file10'] = $document_name10;
        }
        if ($document11) {
            $document_name11 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file11')->getClientOriginalName();
            $request->file('document_file11')->move('uploads/employeeDocuments/', $document_name11);
            $employeeDocument['document_file11'] = $document_name11;
        }


        if ($document12) {
            $document_name12 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file12')->getClientOriginalName();
            $request->file('document_file12')->move('uploads/employeeDocuments/', $document_name12);
            $employeeDocument['document_file12'] = $document_name12;
        }

        if ($document13) {
            $document_name13 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file13')->getClientOriginalName();
            $request->file('document_file13')->move('uploads/employeeDocuments/', $document_name13);
            $employeeDocument['document_file13'] = $document_name13;
        }

        if ($document14) {
            $document_name14 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file14')->getClientOriginalName();
            $request->file('document_file14')->move('uploads/employeeDocuments/', $document_name14);
            $employeeDocument['document_file14'] = $document_name14;
        }

        if ($document15) {
            $document_name15 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file15')->getClientOriginalName();
            $request->file('document_file15')->move('uploads/employeeDocuments/', $document_name15);
            $employeeDocument['document_file15'] = $document_name15;
        }

        if ($document16) {
            $document_name16 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file16')->getClientOriginalName();
            $request->file('document_file16')->move('uploads/employeeDocuments/', $document_name16);
            $employeeDocument['document_file16'] = $document_name16;
        }

        if ($document17) {
            $document_name17 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file17')->getClientOriginalName();
            $request->file('document_file17')->move('uploads/employeeDocuments/', $document_name17);
            $employeeDocument['document_file17'] = $document_name17;
        }

        if ($document18) {
            $document_name18 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file18')->getClientOriginalName();
            $request->file('document_file18')->move('uploads/employeeDocuments/', $document_name18);
            $employeeDocument['document_file18'] = $document_name18;
        }

        if ($document19) {
            $document_name19 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file19')->getClientOriginalName();
            $request->file('document_file19')->move('uploads/employeeDocuments/', $document_name19);
            $employeeDocument['document_file19'] = $document_name19;
        }

        if ($document20) {
            $document_name20 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file20')->getClientOriginalName();
            $request->file('document_file20')->move('uploads/employeeDocuments/', $document_name20);
            $employeeDocument['document_file20'] = $document_name20;
        }

        if ($document21) {
            $document_name21 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file21')->getClientOriginalName();
            $request->file('document_file21')->move('uploads/employeeDocuments/', $document_name21);
            $employeeDocument['document_file21'] = $document_name21;
        }



        $employeeDataFormat  = $this->employeeRepositories->makeEmployeeDocumentInformationDataFormat($request->all());
        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }

        try {
            DB::beginTransaction();
            $employeeAccountDataFormat  = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all(), 'update');
            User::where('user_id', $employee->user_id)->update($employeeAccountDataFormat);

            // Update Personal Information
            $employee->update($employeeData);

            // Delete education qualification
            EmployeeEducationQualification::whereIn('employee_education_qualification_id', explode(',', $request->delete_education_qualifications_cid))->delete();

            // Update Education Qualification
            $employeeEducationData  = $this->employeeRepositories->makeEmployeeEducationDataFormat($request->all(), $id, 'update');
            foreach ($employeeEducationData as $educationValue) {
                $cid = $educationValue['educationQualification_cid'];
                unset($educationValue['educationQualification_cid']);
                if ($cid != "") {
                    EmployeeEducationQualification::where('employee_education_qualification_id', $cid)->update($educationValue);
                } else {
                    $educationValue['employee_id'] = $id;
                    EmployeeEducationQualification::create($educationValue);
                }
            }

            // Delete experience
            EmployeeExperience::whereIn('employee_experience_id', explode(',', $request->delete_experiences_cid))->delete();

            // Update Education Qualification
            $employeeExperienceData  = $this->employeeRepositories->makeEmployeeExperienceDataFormat($request->all(), $id, 'update');
            if (count($employeeExperienceData) > 0) {
                foreach ($employeeExperienceData as $experienceValue) {
                    $cid = $experienceValue['employeeExperience_cid'];
                    unset($experienceValue['employeeExperience_cid']);
                    if ($cid != "") {
                        EmployeeExperience::where('employee_experience_id', $cid)->update($experienceValue);
                    } else {
                        $experienceValue['employee_id'] = $id;
                        EmployeeExperience::create($experienceValue);
                    }
                }
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Employee information successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function show($id)
    {

        $employeeInfo       = Employee::where('employee.employee_id', $id)->first();
        $employeeExperience = EmployeeExperience::where('employee_id', $id)->get();
        $employeeEducation  = EmployeeEducationQualification::where('employee_id', $id)->get();

        return view('admin.user.user.profile', ['employeeInfo' => $employeeInfo, 'employeeExperience' => $employeeExperience, 'employeeEducation' => $employeeEducation]);
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $data = Employee::FindOrFail($id);
            if (!is_null($data->photo)) {
                if (file_exists('uploads/employeePhoto/' . $data->photo) and !empty($data->photo)) {
                    unlink('uploads/employeePhoto/' . $data->photo);
                }
            }
            $result = $data->delete();
            if ($result) {
                // DB::table('user')->where('user_id',$data->user_id)->delete();
                DB::table('user')->where('user_id', $data->user_id)->delete();
                DB::table('employee_education_qualification')->where('employee_id', $data->employee_id)->delete();
                DB::table('employee_experience')->where('employee_id', $data->employee_id)->delete();
                DB::table('employee_attendance')->where('finger_print_id', $data->finger_id)->delete();
                DB::table('employee_award')->where('employee_id', $data->employee_id)->delete();

                DB::table('employee_bonus')->where('employee_id', $data->employee_id)->delete();

                DB::table('promotion')->where('employee_id', $data->employee_id)->delete();

                DB::table('salary_details')->where('employee_id', $data->employee_id)->delete();

                DB::table('training_info')->where('employee_id', $data->employee_id)->delete();

                DB::table('warning')->where('warning_to', $data->employee_id)->delete();

                DB::table('leave_application')->where('employee_id', $data->employee_id)->delete();

                DB::table('employee_performance')->where('employee_id', $data->employee_id)->delete();

                DB::table('termination')->where('terminate_to', $data->employee_id)->delete();

                DB::table('notice')->where('created_by', $data->employee_id)->delete();
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }


    public function bonusdays($employee_id)
    {

        // $employees = DB::select("call `SP_getEmployeeInfo`('" . $employee_id . "')");
        $employees = Employee::where("created_at", ">=", Carbon::now()->subYears(2))->where('status', 1)->get();

        $dataFormat = [];
        $tempArray = [];
        foreach ($employees as $employee) {
            $tempArray['date_of_joining'] = $employee->date_of_joining;
            $tempArray['date_of_leaving'] = $employee->date_of_leaving;
            $tempArray['employee_id'] = $employee->employee_id;
            $tempArray['designation_id'] = $employee->designation_id;
            $tempArray['first_name'] = $employee->first_name;
            $tempArray['last_name'] = $employee->last_name;
            $tempArray['employee_name'] = $employee->first_name . " " . $employee->last_name;
            $tempArray['phone'] = $employee->phone;
            $tempArray['finger_id'] = $employee->finger_id;
            $tempArray['department_id'] = $employee->department_id;


            $date_of_joining = new DateTime($employee->date_of_joining);
            // ->where("created_at", ">=", Carbon::now()->subDays(15))
            // if(){

            // }

            $dataFormat[$employee->employee_id][] = $tempArray;
        }
        return $dataFormat;
    }

    public function downloadFile()
    {
        $file_name = 'templates/employee_details_template.xlsx';
        $file      = Storage::disk('public')->get($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
    public function export()
    {

        $employees = Employee::where('status', UserStatus::$ACTIVE)->with('department', 'branch', 'designation', 'workShift', 'payGrade', 'userName', 'supervisor')->get();
        // dd($employees);

        $extraData = [];
        $inc = 1;
        $supervisor_name = null;
        $shift_name = null;
        $user_name = null;
        $pay_grade_name = null;

        foreach ($employees as $key => $Data) {

            $user = User::find($Data->user_id);

            $role = Role::find($user->role_id);

            if (isset($Data->supervisor_id)) {
                $supervisor = User::find($Data->supervisor_id);
                $supervisor_name = $supervisor->user_name;
            }
            if (isset($Data->work_shift_id)) {
                $shift = WorkShift::find($Data->work_shift_id);
                $shift_name = $shift->shift_name;
            }
            if (isset($Data->user_id)) {
                $user = User::find($Data->user_id);
                $user_name = $user->user_name;
            }
            // if (isset($Data->pay_grade_id)) {
            //     $pay_grade = PayGrade::find($Data->pay_grade_id);
            //     $pay_grade_name = $pay_grade->pay_grade_name;
            // }


            $dataset[] = [
                $inc,
                $user_name,
                $Data->password == 123,
                $role->role_name,
                $Data->finger_id,
                $Data->department->department_name,
                $Data->designation->designation_name,
                $Data->branch->branch_name,
                $supervisor_name,
                $shift_name,
                $Data->esi_card_number,
                $Data->pf_account_number,
                $pay_grade_name,
                $Data->hourly_grade_name,
                (string) $Data->phone,
                $Data->personal_email,
                $Data->first_name,
                $Data->last_name,
                $Data->date_of_birth,
                $Data->date_of_joining,
                $Data->gender,
                $Data->marital_status,
                $Data->address,
                $Data->emergency_contact,
                $Data->blood_group,
                $Data->contact_person_name,
                $Data->relation_of_contact_person,
                $Data->document_title,
                $Data->document_title8,
                $Data->expiry_date8,
                $Data->document_title10,
                $Data->expiry_date10,
                $Data->document_title11,
                $Data->expiry_date11,
                $Data->account_number,
            ];

            $inc++;
        }

        $heading = [

            [
                'SL.NO',
                'USER NAME',
                'PASSWORD',
                'ROLE NAME',
                'FINGER ID',
                'DEPARTMENT',
                'DESIGNATION',
                'BRANCH',
                'SUPERVISIOR',
                'WORK SHIFT',
                'ESI CARD NUMBER',
                'PF ACC NUMBER',
                'PAY GRADE NUMBER',
                'HOURLY GRADE NUMBER',
                'PHONE',
                'EMAIL',
                'FIRST NAME',
                'LASR NAME',
                'DATE OF BIRTH',
                'DATE OF JOINING',
                'GENDER',
                'MARITAL STATUS',
                'ADDRESS',
                'EMERGENCY CONTACT',
                'BLOOD GROUP',
                'CONTACT PERSON',
                'CONTACT PERSON RELATION',
                'AADHAR NO',
                'PASSPORT NO',
                'PASSPORT EXPIRY DATE',
                'DRIVING LICENCE NO',
                'DRIVING LICENCE EXPIRY DATE',
                'CIVIL ID',
                'CIVIL ID EXPIRY DATE',
                'ACCOUNT NO',

            ],
        ];

        $extraData['heading'] = $heading;

        $filename = 'EmployeeInfo-' . DATE('d-m-Y His') . '.xlsx';

        $response = Excel::download(new EmployeeDetailsExport($dataset, $extraData), $filename);
        ob_end_clean();
        return $response;
    }
    public function photoimport()
    {
        return view('admin.employee.employee.photoimport', []);
    }

    public function photoimportstore(Request $request)
    {


        // dd($request);
        $request->validate(
            [
                'photo' => 'required|max:10',
                'photo.*' => 'mimes:jpeg,jpg,png|max:1192|dimensions:max_width=500,max_height=600',
            ],
            [
                'photo.required' => 'Photo field required field is required',
                'photo.max' => 'The photo must not be greater than 10',
                'photo.dimensions.*' => 'Photo Dimensions shold be Max Width:500px & Max Height:600px',
                'photo.max.*' => 'Photo must not be greater than 1 MB'
            ]
        );

        $files = [];
        $input = $request->all();
        if ($request->hasfile('photo')) {

            foreach ($request->file('photo') as $pic) {
                $pic_name = explode(".", $pic->getClientOriginalName());
                $reg_no = $pic_name[0];
                $pic_name = $pic_name[0] . "." . $pic->getClientOriginalExtension();
                $pic->move('uploads/photo', $pic_name);
                $photo = new Photos;
                $photo->name = $pic_name;
                $photo->register_no = $reg_no;
                $photo->status = 1;
                $photo->save();
            }
        }


        /*$input['photo'] = $pic_name;
        $input['name'] = $request->first_name . " " . $request->last_name;
        $input['dob'] = DATE('Y-m-d', strtotime($request->dob));
        $input['device_uniqueid'] = $request->register_no;
        Student::create($input);*/
        return redirect()->route('photo.import')->with('success', 'Photo details saved successfully');
    }

    // public function photodetails()
    // {

    //     $data = Photos::where('status', 1)->orderBy('photo_id', 'DESC');
    //     return DataTables::of($data)
    //         ->addColumn('action', function ($data) {

    //             return '<a href="#" class="btn btn-xs btn-danger photo-dlt" title="Delete" data-id="' . $data->photo_id . '"><i class="fa fa-trash"></i></a>';
    //         })
    //         ->editColumn('name', function ($data) {
    //             $expl = explode(".", $data->name);
    //             return $expl[0];
    //         })
    //         ->editColumn('photo', function ($data) {
    //             if ($data->name)
    //                 return '<img src="../uploads/photo/' . $data->name . '" style="width:50px;">';
    //         })

    //         ->editColumn('created_at', function ($data) {
    //             return DATE('d-m-Y h:i A', strtotime($data->created_at));
    //         })
    //         ->rawColumns(['action', 'photo'])
    //         ->make(true);
    // }


    public function photodelete(Request $request)
    {

        $photo = Photos::where('photo_id', $request->id)->first();
        $photo->status = 2;
        $photo->save();
        echo "deleted";
    }
}
