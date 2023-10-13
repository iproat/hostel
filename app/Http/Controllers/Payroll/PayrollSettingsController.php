<?php

namespace App\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Payroll;
use App\Model\Designation;
use App\Model\PayrollSettings;
use \Mpdf\Mpdf as PDF;
use App\Http\Controllers\Controller;

class PayrollSettingsController extends Controller{
    
    public function index(Request $request){
        $settings=PayrollSettings::find(1);
        return view('admin.payroll.settings.form',compact('settings'));
    }

    public function store(Request $request){

        $request->validate([
            'basic'=>'required|numeric|min:0|max:100',
            'hra'=>'required|numeric|min:0|max:100',
            'employee_esic'=>'required|numeric|min:0|max:100',
            'employee_pf'=>'required|numeric|min:0|max:100',
            'working_days'=>'numeric|min:0|max:100',
            // 'working_hours'=>'numeric|min:0|max:100',
        ]);
        $settings=PayrollSettings::find(1);
        $settings->update($request->all());

        return redirect(route('payroll.settings'))->with('success', 'Payroll settings successfully updated.');
    }


}
