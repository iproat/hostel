<?php

namespace App\Imports;

use App\User;
use App\Model\Role;
use App\Model\Branch;
use App\Model\Employee;
use App\Model\Department;
use App\Model\Designation;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport  implements ToModel, WithValidation, WithStartRow
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.1' => 'required|unique:user,user_name',
            '*.2' => 'required|exists:role,role_name',
            '*.3' => 'required|unique:employee,finger_id',
            '*.4' => 'required|exists:department,department_name',
            '*.5' => 'required|exists:designation,designation_name',
            '*.6' => 'required|exists:branch,branch_name',
            '*.7' => 'required|exists:user,user_name',
            // '*.8' => 'required|exists:work_shift,shift_name',           
            '*.11' => 'required',
            '*.12' => 'required',
            '*.13' => 'required',

        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Sr.No is required',
            '1.required' => 'User name is required',
            '2.required' => 'Role name should be same as the name provided in employee management module',
            '3.required' => 'Finger Id (ie: Employee No) is required',
            '3.unique' => 'Finger Id should be unique',
            '4.required' => 'Department Name should be same as the name provided in employee management module',
            '5.required' => 'Designation Name should be same as the name provided in employee management module',
            '6.required' => 'Branch Name should be same as the name provided in employee management module',
            '7.required' => 'Supervisor Name should be same as the  user name provided in employee management module',
            // '8.required' => 'Workshift Name should be same as the name provided in attendance management module ',           
            '11.required' => 'Employee first name is required',
            '12.required' => 'Employee last name is required',
            '13.in' => 'Invalid Gender ,can user only Male / Female ',
            '14.in' => 'Invalid Marital status ,can user only use Married / Unmarried ',
            '15.required' => 'Address is required',
            '1.unique' => 'User name should be unique',
            '2.exists' => 'Role name doest not exists',
            '4.exists' => 'Department name doest not exists',
            '5.exists' => 'Designation name doest not exists',
            '6.exists' => 'Branch name doest not exists',
            '7.exists' => 'Supervisor user name doest not exists',
            // '8.exists' => 'Workshift name doest not exists',
        ];
    }

    public function model(array $row)
    {
        try {
            DB::beginTransaction();
            $rollno = $row[3];
            $filepath = storage_path('../uploads/photo/' . $rollno . '.png');
            if (!file_exists($filepath))
                $filepath = storage_path('../uploads/photo/' . $rollno . '.jpg');
            $filename = '';
            if (file_exists($filepath)) {
                $extension = pathinfo($filepath);
                $filename = DATE('YmdHis') . "_" . $rollno . '.' . $extension['extension'];
                copy($filepath, storage_path('../uploads/employeePhoto/' . $filename));
                //@unlink($filepath);

                \App\Model\Photos::where('register_no', $rollno)->update(['status' => 2]);
            }

            $role = Role::where('role_name', $row[2])->first();

            $userData = User::create([
                'user_name' => $row[1],
                'role_id' => $role->role_id,
                'password' => Hash::make('demo1234'),
                'status' => UserStatus::$ACTIVE,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
            

            $user = User::where('user_name', $row[7])->first();
            $dept = Department::where('department_name', $row[4])->first();
            $design = Designation::where('designation_name', $row[5])->first();
            $emp = Employee::where('user_id', $user->user_id)->first();
            // $shift = WorkShift::where('shift_name', $row[8])->first();
            $branch = Branch::where('branch_name', $row[6])->first();

            $employeeData = Employee::create([
                'user_id' => $userData->user_id,
                'finger_id' => $row[3],
                'department_id' => $dept->department_id,
                'designation_id' => $design->designation_id,
                'branch_id' => $branch->branch_id,
                'supervisor_id' => $emp->employee_id,
                // 'work_shift_id' => $shift->work_shift_id,
                'phone' => $row[9],
                'email' => $row[10],
                'first_name' => $row[11],
                'last_name' => $row[12],
                'gender' => $row[13],
                'marital_status' => $row[14],
                'address' => $row[15],
                'photo' => $filename,
                'status' => 1,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
            $bug = 0;
            DB::commit();
        } catch (\Throwable $th) {
            $bug = 1;
            dd($th);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
