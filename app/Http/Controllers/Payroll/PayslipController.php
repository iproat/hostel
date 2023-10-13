<?php

namespace App\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Components\Common;
use App\Model\Payroll;
use App\Model\Designation;
use App\Model\Department;
use App\Model\PayrollSettings;
use \Mpdf\Mpdf as PDF;
use App\Http\Controllers\Controller;

class PayslipController extends Controller{
    
    public function index(Request $request){

        $payroll=Payroll::find($request->id);
        $employee=Employee::find($payroll->employee);
        $settings=PayrollSettings::where('payset_id',1)->first();	
        $workingDays = Common::workingDays();
        $designation =  Designation::where('designation_id',$employee->designation_id)->first();	
        $basic = 	round($payroll->gross_salary *($settings->basic/100));
        // Setup a filename 
        $documentFileName = "Payslip.pdf";
 
        // Create the mPDF document
        $document = new PDF( [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '2',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);     
 
        // Set some header informations for output
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$documentFileName.'"'
        ];
 
        // Write some simple Content
        $document->WriteHTML(view('admin.payroll.payslip.pdfexport',['document'=>$document,'payroll'=>$payroll,'employee'=>$employee,'settings'=>$settings,
        'workingdays'=>$workingDays,'designation'=> $designation,'basic'=> $basic]));


        return $document->Output($documentFileName,"I");
         
        /*// Save PDF on your public storage 
        Storage::disk('public')->put($documentFileName, $document->Output($documentFileName, "S"));
         
        // Get file back from storage with the give header informations
        return Storage::disk('public')->download($documentFileName, 'Request', $header); //*/
    }
    public function list(Request $request){

        $payroll=Payroll::where('status','1')->get();
        
        return view('admin.payroll.payslip.list', ['payrolldata' => $payroll]);
    }
    public function payrolllist(Request $request){       
        
        return view('admin.payroll.payslip.payrolllist');
    }
    public function pdfexport(Request $request){ 
        $payroll=Payroll::find($request->emp);
        $employee=Employee::find($payroll->employee); 
        $settings=PayrollSettings::where('payset_id',1)->first();	
        $workingDays = Common::workingDays();
        $designation =  Designation::where('designation_id',$employee->designation_id)->first();	
        $basic = 	round($payroll->gross_salary *($settings->basic/100));
        return view('admin.payroll.payslip.pdfexport',['payroll'=>$payroll,'employee'=>$employee,'settings'=>$settings,
        'workingdays'=>$workingDays,'designation'=> $designation,'basic'=> $basic]);
    }
    


}
