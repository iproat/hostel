<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $fillable = [
        'employee_id', 'device_employee_id', 'user_id', 'finger_id', 'department_id',
        'designation_id', 'branch_id', 'supervisor_id',
        'work_shift_id', 'personal_email', 'official_email',
        'blood_group', 'first_name', 'last_name',
        'date_of_birth', 'date_of_joining', 'date_of_leaving',
        'gender', 'marital_status', 'photo', 'address',
        'emergency_contact', 'contact_person_name',
        'relation_of_contact_person', 'phone',
        'document_title', 'document_name',
        'document_title2', 'document_name2',
        'document_title3', 'document_name3',
        'document_title4', 'document_name4',
        'document_title5', 'document_name5',
        'document_title6', 'document_name6',
        'document_title7', 'document_name7',
        'document_title8', 'document_name8', 'expiry_date8',
        'document_title9', 'document_name9', 'expiry_date9',
        'document_title10', 'document_name10', 'expiry_date10',
        'document_title11', 'document_name11', 'expiry_date11',
        'gross_salary', 'percentage_basic',
        'basic_salary', 'over_time_rate',
        'hra', 'conveyance', 'medical_allowance',
        'shift_allowance', 'incentive', 'medical_insurance',
        'other_allowance', 'variable_pay', 'deduction_of_epf',
        'deduction_of_esic', 'professional_tax', 'net_pay',
        'ctc', 'monthly_ctc', 'employer_esic',
        'account_number', 'ifsc_number',
        'name_of_the_bank', 'account_holder',
        'status', 'created_by',
        'updated_by', 'faith', 'pay_grade_id',
        'hourly_salaries_id', 'esi_card_number',
        'pf_account_number', 'region',
        'card_title1', 'card_number1', 'card_title2', 'card_number2', 'card_title3', 'card_number3', 'card_title4', 'card_number4', 'card_title5', 'card_number5',

        'document_title12', 'document_number12', 'document_name12',
        'document_title13', 'document_number13', 'document_name13',
        'document_title14', 'document_number14', 'document_name14',
        'document_title15', 'document_number15', 'document_name15',

        'document_title16', 'document_name16', 'expiry_date16',
        'document_title17', 'document_name17', 'expiry_date17',
        'document_title18', 'document_name18', 'expiry_date18',
        'document_title19', 'document_name19', 'expiry_date19',
        'document_title20', 'document_name20', 'expiry_date20',
        'document_title21', 'document_name21', 'expiry_date21',
    ];
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeFilter($query, $request)
    {
        return $query->where('department_id', $request['department_id'])->where('branch_id', $request['branch_id'])->where('employee_id', $request['employee_id']);
    }
    public function userName()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payGrade()
    {
        return $this->belongsTo(PayGrade::class, 'pay_grade_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hourlySalaries()
    {
        return $this->belongsTo(HourlySalary::class, 'hourly_salaries_id');
    }

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }

    public function payroll()
    {
        return $this->hasOne(PayRoll::class, 'employee', 'employee_id');
    }

    public function scopePayroll($query, $request)
    {
        return $query->where('employee', $request->employee_id)->orWhere('finger_print_id', $request->finger_print_id);
    }
}
