<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Model\Employee;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository
{

    public function makeEmployeeAccountDataFormat($data, $action = false)
    {

        $employeeAccountData['role_id'] = $data['role_id'];

        if ($action != 'update') {

            $employeeAccountData['password'] = Hash::make($data['password']);
        }

        $employeeAccountData['user_name'] = $data['user_name'];

        $employeeAccountData['status'] = $data['status'];

        $employeeAccountData['created_by'] = Auth::user()->user_id;

        $employeeAccountData['updated_by'] = Auth::user()->user_id;

        return $employeeAccountData;
    }

    public function makeEmployeePersonalInformationDataFormat($data)
    {

        $employeeData['first_name'] = $data['first_name'];

        $employeeData['last_name'] = $data['last_name'];

        $employeeData['finger_id'] = $data['finger_id'];

        // $employeeData['document_title'] = $data['document_title'];

        // if (isset($data['document_file'])) {
        //     $employeeData['document_name'] = date('Y_m_d_H_i_s') . '_' . $data['document_file']->getClientOriginalName();
        // }

        // $employeeData['document_title2'] = $data['document_title2'];

        // if (isset($data['document_file2'])) {
        //     $employeeData['document_name2'] = date('Y_m_d_H_i_s') . '_' . $data['document_file2']->getClientOriginalName();
        // }

        // $employeeData['document_title3'] = $data['document_title3'];

        // if (isset($data['document_file3'])) {
        //     $employeeData['document_name3'] = date('Y_m_d_H_i_s') . '_' . $data['document_file3']->getClientOriginalName();
        // }

        // $employeeData['document_title4'] = $data['document_title4'];

        // if (isset($data['document_file4'])) {
        //     $employeeData['document_name4'] = date('Y_m_d_H_i_s') . '_' . $data['document_file4']->getClientOriginalName();
        // }

        // $employeeData['document_title5'] = $data['document_title5'];

        // if (isset($data['document_file5'])) {
        //     $employeeData['document_name5'] = date('Y_m_d_H_i_s') . '_' . $data['document_file5']->getClientOriginalName();
        // }

        $employeeData['department_id'] = $data['department_id'];

        $employeeData['designation_id'] = $data['designation_id'];

        $employeeData['branch_id'] = $data['branch_id'];

        $employeeData['supervisor_id'] = $data['supervisor_id'];

        $employeeData['work_shift_id'] = $data['work_shift_id'];

        $employeeData['esi_card_number'] = $data['esi_card_number'];

        $employeeData['pf_account_number'] = $data['pf_account_number'];

        $employeeData['pay_grade_id'] = $data['pay_grade_id'];

        $employeeData['hourly_salaries_id'] = $data['hourly_salaries_id'];

        // $employeeData['personal_email'] = $data['personal_email'];
        // $employeeData['official_email'] = $data['official_email'];
        // $employeeData['blood_group'] = $data['blood_group'];

        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);

        $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);

        $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);

        $employeeData['marital_status'] = $data['marital_status'];

        $employeeData['address'] = $data['address'];

        $employeeData['emergency_contacts'] = $data['emergency_contacts'];

        $employeeData['gender'] = $data['gender'];

        $employeeData['religion'] = $data['religion'];

        $employeeData['phone'] = $data['phone'];

        $employeeData['status'] = $data['status'];

        $employeeData['created_by'] = Auth::user()->user_id;

        $employeeData['updated_by'] = Auth::user()->user_id;

        return $employeeData;
    }

    public function makeEmployeeDocumentInformationDataFormat($data)
    {
        // dd($data);

        $employeeData['first_name'] = $data['first_name'];

        $employeeData['last_name'] = $data['last_name'];

        $employeeData['finger_id'] = $data['finger_id'];

        // $employeeData['document_title'] = $data['document_title'];


        // if (isset($data['document_file'])) {
        //     $employeeData['document_name'] = date('Y_m_d_H_i_s') . '_' . $data['document_file']->getClientOriginalName();
        // }
        // $employeeData['document_title2'] = $data['document_title2'];

        // if (isset($data['document_file2'])) {
        //     $employeeData['document_name2'] = date('Y_m_d_H_i_s') . '_' . $data['document_file2']->getClientOriginalName();
        // }

        // $employeeData['document_title3'] = $data['document_title3'];

        // if (isset($data['document_file3'])) {
        //     $employeeData['document_name3'] = date('Y_m_d_H_i_s') . '_' . $data['document_file3']->getClientOriginalName();
        // }

        // $employeeData['document_title4'] = $data['document_title4'];

        // if (isset($data['document_file4'])) {
        //     $employeeData['document_name4'] = date('Y_m_d_H_i_s') . '_' . $data['document_file4']->getClientOriginalName();
        // }

        // $employeeData['document_title5'] = $data['document_title5'];

        // if (isset($data['document_file5'])) {
        //     $employeeData['document_name5'] = date('Y_m_d_H_i_s') . '_' . $data['document_file5']->getClientOriginalName();
        // }
        // $employeeData['document_title6'] = $data['document_title6'];

        // if (isset($data['document_file6'])) {
        //     $employeeData['document_name6'] = date('Y_m_d_H_i_s') . '_' . $data['document_file6']->getClientOriginalName();
        // }
        // $employeeData['document_title7'] = $data['document_title7'];

        // if (isset($data['document_file7'])) {
        //     $employeeData['document_name7'] = date('Y_m_d_H_i_s') . '_' . $data['document_file7']->getClientOriginalName();
        // }
        // $employeeData['document_title8'] = $data['document_title8'];

        // if (isset($data['document_file8'])) {
        //     $employeeData['document_name8'] = date('Y_m_d_H_i_s') . '_' . $data['document_file8']->getClientOriginalName();
        // }
        // $employeeData['document_title9'] = $data['document_title9'];

        // if (isset($data['document_file9'])) {
        //     $employeeData['document_name9'] = date('Y_m_d_H_i_s') . '_' . $data['document_file9']->getClientOriginalName();
        // }
        // $employeeData['document_title10'] = $data['document_title10'];

        // if (isset($data['document_file10'])) {
        //     $employeeData['document_name10'] = date('Y_m_d_H_i_s') . '_' . $data['document_file10']->getClientOriginalName();
        // }
        // $employeeData['document_title11'] = $data['document_title11'];

        // if (isset($data['document_file11'])) {
        //     $employeeData['document_name11'] = date('Y_m_d_H_i_s') . '_' . $data['document_file11']->getClientOriginalName();
        // }

        // $employeeData['document_title12'] = $data['document_title12'];
        // $employeeData['document_number12'] = $data['document_number12'];

        // if (isset($data['document_file12'])) {
        //     $employeeData['document_name12'] = date('Y_m_d_H_i_s') . '_' . $data['document_file12']->getClientOriginalName();
        // }

        // $employeeData['document_title13'] = $data['document_title13'];
        // $employeeData['document_number13'] = $data['document_number13'];

        // if (isset($data['document_file13'])) {
        //     $employeeData['document_name13'] = date('Y_m_d_H_i_s') . '_' . $data['document_file13']->getClientOriginalName();
        // }

        // $employeeData['document_title14'] = $data['document_title14'];
        // $employeeData['document_number14'] = $data['document_number14'];

        // if (isset($data['document_file14'])) {
        //     $employeeData['document_name14'] = date('Y_m_d_H_i_s') . '_' . $data['document_file14']->getClientOriginalName();
        // }

        // $employeeData['document_title15'] = $data['document_title15'];
        // $employeeData['document_number15'] = $data['document_number15'];

        // if (isset($data['document_file15'])) {
        //     $employeeData['document_name15'] = date('Y_m_d_H_i_s') . '_' . $data['document_file15']->getClientOriginalName();
        // }

        // $employeeData['document_title16'] = $data['document_title16'];

        // if (isset($data['document_file16'])) {
        //     $employeeData['document_name16'] = date('Y_m_d_H_i_s') . '_' . $data['document_file16']->getClientOriginalName();
        // }

        // $employeeData['document_title17'] = $data['document_title17'];

        // if (isset($data['document_file17'])) {
        //     $employeeData['document_name17'] = date('Y_m_d_H_i_s') . '_' . $data['document_file17']->getClientOriginalName();
        // }

        // $employeeData['document_title18'] = $data['document_title18'];

        // if (isset($data['document_file18'])) {
        //     $employeeData['document_name18'] = date('Y_m_d_H_i_s') . '_' . $data['document_file18']->getClientOriginalName();
        // }

        // $employeeData['document_title19'] = $data['document_title19'];
        
        // if (isset($data['document_file19'])) {
        //     $employeeData['document_name19'] = date('Y_m_d_H_i_s') . '_' . $data['document_file19']->getClientOriginalName();
        // }

        // $employeeData['document_title20'] = $data['document_title20'];

        // if (isset($data['document_file20'])) {
        //     $employeeData['document_name20'] = date('Y_m_d_H_i_s') . '_' . $data['document_file20']->getClientOriginalName();
        // }

        // $employeeData['document_title21'] = $data['document_title21'];

        // if (isset($data['document_file21'])) {
        //     $employeeData['document_name21'] = date('Y_m_d_H_i_s') . '_' . $data['document_file21']->getClientOriginalName();
        // }


        // $employeeData['expiry_date8'] = dateConvertFormtoDB($data['expiry_date8']);
        // $employeeData['expiry_date9'] = dateConvertFormtoDB($data['expiry_date9']);
        // $employeeData['expiry_date10'] = dateConvertFormtoDB($data['expiry_date10']);
        // $employeeData['expiry_date11'] = dateConvertFormtoDB($data['expiry_date11']);

        // /*$employeeData['expiry_date12'] = dateConvertFormtoDB($data['expiry_date12']);
        // $employeeData['expiry_date13'] = dateConvertFormtoDB($data['expiry_date13']);
        // $employeeData['expiry_date14'] = dateConvertFormtoDB($data['expiry_date14']);
        // $employeeData['expiry_date15'] = dateConvertFormtoDB($data['expiry_date15']);*/
        // $employeeData['expiry_date16'] = dateConvertFormtoDB($data['expiry_date16']);
        // $employeeData['expiry_date17'] = dateConvertFormtoDB($data['expiry_date17']);
        // $employeeData['expiry_date18'] = dateConvertFormtoDB($data['expiry_date18']);
        // $employeeData['expiry_date19'] = dateConvertFormtoDB($data['expiry_date19']);
        // $employeeData['expiry_date20'] = dateConvertFormtoDB($data['expiry_date20']);
        // $employeeData['expiry_date21'] = dateConvertFormtoDB($data['expiry_date21']);

        $employeeData['department_id'] = $data['department_id'];

        $employeeData['designation_id'] = $data['designation_id'];

        $employeeData['branch_id'] = $data['branch_id'];

        $employeeData['supervisor_id'] = $data['supervisor_id'];

        // $employeeData['work_shift_id'] = $data['work_shift_id'];

        // $employeeData['esi_card_number'] = $data['esi_card_number'];

        // $employeeData['pf_account_number'] = $data['pf_account_number'];

        // $employeeData['card_title1'] = $data['card_title1'];
        // $employeeData['card_number1'] = $data['card_number1'];

        // $employeeData['card_title2'] = $data['card_title2'];
        // $employeeData['card_number2'] = $data['card_number2'];

        // $employeeData['card_title3'] = $data['card_title3'];
        // $employeeData['card_number3'] = $data['card_number3'];

        // $employeeData['card_title4'] = $data['card_title4'];
        // $employeeData['card_number4'] = $data['card_number4'];

        // $employeeData['card_title5'] = $data['card_title5'];
        // $employeeData['card_number5'] = $data['card_number5'];


        // $employeeData['pay_grade_id'] = $data['pay_grade_id'];

        // $employeeData['hourly_salaries_id'] = $data['hourly_salaries_id'];

        // $employeeData['personal_email'] = $data['personal_email'];

        // $employeeData['official_email'] = $data['official_email'];

        // $employeeData['blood_group'] = $data['blood_group'];

        // $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);

        // $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);

        // $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);

        // $employeeData['marital_status'] = $data['marital_status'];

        $employeeData['address'] = $data['address'];

        $employeeData['gender'] = $data['gender'];

        // $employeeData['faith'] = $data['faith'];

        // $employeeData['phone'] = $data['phone'];
        // $employeeData['gross_salary'] = $data['gross_salary'];
        // $employeeData['basic_salary'] = $data['basic_salary'];
        // $employeeData['hra'] = $data['hra'];
        // $employeeData['conveyance'] = $data['conveyance'];
        // $employeeData['medical_allowance'] = $data['medical_allowance'];
        // $employeeData['shift_allowance'] = $data['shift_allowance'];
        // $employeeData['incentive'] = $data['incentive'];
        // $employeeData['medical_insurance'] = $data['medical_insurance'];
        // $employeeData['other_allowance'] = $data['other_allowance'];
        // $employeeData['variable_pay'] = $data['variable_pay'];
        // $employeeData['deduction_of_epf'] = $data['deduction_of_epf'];
        // $employeeData['deduction_of_esic'] = $data['deduction_of_esic'];
        // $employeeData['professional_tax'] = $data['professional_tax'];
        // $employeeData['net_pay'] = $data['net_pay'];
        // $employeeData['ctc'] = $data['ctc'];
        // $employeeData['monthly_ctc'] = $data['monthly_ctc'];
        // $employeeData['employer_esic'] = $data['employer_esic'];

       // $employeeData['account_number'] = $data['account_number'];

       // $employeeData['ifsc_number'] = $data['ifsc_number'];

       // $employeeData['name_of_the_bank'] = $data['name_of_the_bank'];

      //  $employeeData['account_holder'] = $data['account_holder'];

        // $employeeData['emergency_contact'] = $data['emergency_contact'];

        // $employeeData['contact_person_name'] = $data['contact_person_name'];

        // $employeeData['relation_of_contact_person'] = $data['relation_of_contact_person'];

        $employeeData['status'] = $data['status'];
      //  $employeeData['region'] = $data['region'];

        $employeeData['created_by'] = Auth::user()->user_id;

        $employeeData['updated_by'] = Auth::user()->user_id;

        return $employeeData;
    }

    public function makeEmployeeEducationDataFormat($data, $employee_id, $action = false)
    {

        $educationData = [];

        if (isset($data['institute'])) {

            for ($i = 0; $i < count($data['institute']); $i++) {

                $educationData[$i] = [

                    'employee_id'      => $employee_id,

                    'institute'        => $data['institute'][$i],

                    'board_university' => $data['board_university'][$i],

                    'degree'           => $data['degree'][$i],

                    'passing_year'     => $data['passing_year'][$i],

                    'result'           => $data['result'][$i],

                    'cgpa'             => $data['cgpa'][$i],

                ];

                if ($action == 'update') {

                    $educationData[$i]['educationQualification_cid'] = $data['educationQualification_cid'][$i];
                }
            }
        }

        return $educationData;
    }

    public function makeEmployeePaygradeDataFormat($data, $employee_id, $action = false)
    {

        $paygradeData = [];
        if (isset($data['employee_name'])) {
            for ($i = 0; $i < count($data['employee_name']); $i++) {
                $paygradeData[$i] = [
                    'employee_id'         => $employee_id,
                    'gross_salary'        => $data['gross_salary'][$i],
                    'percentage_of_basic' => $data['percentage_of_basic'][$i],
                    'basic_salary'        =>  $data['basic_salary'][$i],
                    'over_time'           => $data['over_time'][$i],
                    'allowance_id'        => $data['allowance_id'][$i],
                    'deduction_id'        => $data['deduction_id'][$i],
                ];

                if ($action == 'update') {
                    $paygradeData[$i]['employeePayGrade_pid'] = $data['employeePayGrade_pid'][$i];
                }
            }
        }

        return $paygradeData;
    }
    public function makeEmployeeExperienceDataFormat($data, $employee_id, $action = false)
    {

        $experienceData = [];

        if (isset($data['organization_name'])) {

            for ($i = 0; $i < count($data['organization_name']); $i++) {

                $experienceData[$i] = [

                    'employee_id'       => $employee_id,

                    'organization_name' => $data['organization_name'][$i],

                    'designation'       => $data['designation'][$i],

                    'from_date'         => dateConvertFormtoDB($data['from_date'][$i]),

                    'to_date'           => dateConvertFormtoDB($data['to_date'][$i]),

                    'responsibility'    => $data['responsibility'][$i],

                    'skill'             => $data['skill'][$i],

                ];

                if ($action == 'update') {

                    $experienceData[$i]['employeeExperience_cid'] = $data['employeeExperience_cid'][$i];
                }
            }
        }

        return $experienceData;
    }
    public function makeEmployeeBankDetailsDataFormat($data, $employee_id, $action = false)
    {

        $bankdetailsData = [];

        if (isset($data['Bank_details'])) {

            for ($i = 0; $i < count($data['Bank_details']); $i++) {

                $bankdetailsData[$i] = [

                    'employee_id'       => $employee_id,

                    'account_number' => $data['account_number'][$i],

                    'ifsc_number'       => $data['ifsc_number'][$i],

                    'name_of_the_bank'    => $data['name_of_the_bank'][$i],

                    'account_holder'             => $data['account_holder'][$i],

                ];

                if ($action == 'update') {

                    $bankdetailsData[$i]['employeeBankDetails_bid'] = $data['employeeBankDetails_bid'][$i];
                }
            }
        }

        return $bankdetailsData;
    }

    public function bonusDayEligibility()
    {

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'date_of_joining', 'date_of_leaving', 'finger_id', 'employee_id', 'branch_name')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', '=', 'employee.department_id')
            ->join('branch', 'branch.branch_id', '=', 'employee.branch_id')
            ->where('status', UserStatus::$ACTIVE)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'asc')->get();
        $dataFormat = [];
        $tempArray  = [];
        if (count($employees) > 0) {
            foreach ($employees as $employee) {
                $tempArray['date_of_joining']  = $employee->date_of_joining;
                $tempArray['date_of_leaving']  = $employee->date_of_leaving;
                $tempArray['employee_id']      = $employee->employee_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['fullName']         = $employee->fullName;
                $tempArray['phone']            = $employee->phone;
                $tempArray['finger_id']        = $employee->finger_id;
                $tempArray['department_name']  = $employee->department_name;
                $tempArray['branch_name']      = $employee->branch_name;

                $dataFormat[$employee->employee_id][] = $tempArray;
            }
        } else {
            $tempArray['status'] = 'No Data Found';
            $dataFormat[]        = $tempArray['status'];
        }
        return $dataFormat;
    }
}
