<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Model\GeneralSettings;

class DocumentExpiryController extends Controller
{
    public function IfDocumentsExpiry()
    {

        set_time_limit(0);

        $date = DATE('Y-m-d');
        $allEmployee = DB::table('employee')
            ->where('status', 1)
            ->whereRaw(' ( DATE_SUB(expiry_date8,INTERVAL 1 MONTH)  <= "' . $date . '" AND expiry_date8 IS NOT NULL ) or  
                         ( DATE_SUB(expiry_date9,INTERVAL 1 MONTH)  <=  "' . $date . '" AND expiry_date9 IS NOT NULL ) or  
                         ( DATE_SUB(expiry_date11,INTERVAL 1 MONTH)  <=  "' . $date . '" AND expiry_date11 IS NOT NULL ) or  
                         ( DATE_SUB(expiry_date10,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date10 IS NOT NULL ) or  
                         ( DATE_SUB(expiry_date16,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date16 IS NOT NULL ) or
                         ( DATE_SUB(expiry_date17,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date17 IS NOT NULL ) or
                         ( DATE_SUB(expiry_date18,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date18 IS NOT NULL ) or
                         ( DATE_SUB(expiry_date19,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date19 IS NOT NULL ) or
                         ( DATE_SUB(expiry_date20,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date20 IS NOT NULL ) or
                         ( DATE_SUB(expiry_date21,INTERVAL 1 MONTH) <=  "' . $date . '" AND expiry_date21 IS NOT NULL )
                    ')
            ->select('employee_id', 'first_name', 'personal_email', 'official_email', 'expiry_date8', 'expiry_date9', 'expiry_date10', 'expiry_date11', 'expiry_date16', 'expiry_date17', 'expiry_date18', 'expiry_date19', 'expiry_date20', 'expiry_date21', 'document_title16', 'document_title17', 'document_title18', 'document_title19', 'document_title20', 'document_title21', 'last_name')->get();

        //  AND expiry_date8 >="'.$date.'" AND expiry_date9 >="'.$date.'"   AND expiry_date10 >="'.$date.'" AND expiry_date11 >="'.$date.'"

        // dd($allEmployee);
        //exit;
        $msg = '';
        $ex_info = $info = $emp_info = [];

        //dd($allEmployee);

        foreach ($allEmployee as $key => $Data) {


            if (DATE('Y-m-d', strtotime($Data->expiry_date8 . "-1 MONTHS"))  <= $date && $Data->expiry_date8 >= $date) {
                $info[] = $this->expire('Passport', $Data->expiry_date8, $Data);
            } elseif (!is_null($Data->expiry_date8) && DATE('Y-m-d', strtotime($Data->expiry_date8 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire('Passport', $Data->expiry_date8, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date9 . "-1 MONTHS"))  <= $date && $Data->expiry_date9 >= $date) {
                $info[] = $this->expire('Visa', $Data->expiry_date9, $Data);
            } elseif (!is_null($Data->expiry_date9) && DATE('Y-m-d', strtotime($Data->expiry_date9 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire('Visa', $Data->expiry_date9, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date10 . "-1 MONTHS"))  <= $date && $Data->expiry_date10 >= $date) {
                $info[] = $this->expire('Driving License', $Data->expiry_date10, $Data);
            } elseif (!is_null($Data->expiry_date10) &&  DATE('Y-m-d', strtotime($Data->expiry_date10 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire('Driving License', $Data->expiry_date10, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date11 . "-1 MONTHS"))  <= $date && $Data->expiry_date11 >= $date) {
                $info[] = $this->expire('Resident Card', $Data->expiry_date11, $Data);
            } elseif (!is_null($Data->expiry_date11) && DATE('Y-m-d', strtotime($Data->expiry_date11 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire('Resident Card', $Data->expiry_date11, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date16 . "-1 MONTHS"))  <= $date && $Data->expiry_date16 >= $date) {
                $info[] = $this->expire($Data->document_title16, $Data->expiry_date16, $Data);
            } elseif (!is_null($Data->expiry_date16) && DATE('Y-m-d', strtotime($Data->expiry_date16 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title16, $Data->expiry_date16, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date17 . "-1 MONTHS"))  <= $date && $Data->expiry_date17 >= $date) {
                $info[] = $this->expire($Data->document_title17, $Data->expiry_date17, $Data);
            } elseif (!is_null($Data->expiry_date17) && DATE('Y-m-d', strtotime($Data->expiry_date17 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title17, $Data->expiry_date17, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date18 . "-1 MONTHS"))  <= $date && $Data->expiry_date18 >= $date) {
                $info[] = $this->expire($Data->document_title18, $Data->expiry_date18, $Data);
            } elseif (!is_null($Data->expiry_date18) && DATE('Y-m-d', strtotime($Data->expiry_date18 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title18, $Data->expiry_date18, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date19 . "-1 MONTHS"))  <= $date && $Data->expiry_date19 >= $date) {
                $info[] = $this->expire($Data->document_title19, $Data->expiry_date19, $Data);
            } elseif (!is_null($Data->expiry_date19) && DATE('Y-m-d', strtotime($Data->expiry_date19 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title19, $Data->expiry_date19, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date20 . "-1 MONTHS"))  <= $date && $Data->expiry_date20 >= $date) {
                $info[] = $this->expire($Data->document_title20, $Data->expiry_date20, $Data);
            } elseif (!is_null($Data->expiry_date20) && DATE('Y-m-d', strtotime($Data->expiry_date20 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title20, $Data->expiry_date20, $Data);
            }

            if (DATE('Y-m-d', strtotime($Data->expiry_date21 . "-1 MONTHS"))  <= $date && $Data->expiry_date21 >= $date) {
                $info[] = $this->expire($Data->document_title21, $Data->expiry_date21, $Data);
            } elseif (!is_null($Data->expiry_date21) && DATE('Y-m-d', strtotime($Data->expiry_date21 . "-1 MONTHS"))  <= $date) {
                $ex_info[] = $this->expire($Data->document_title21, $Data->expiry_date21, $Data);
            }


            $data = ['name' => $Data->first_name, 'content' => $info, 'expired_content' => $ex_info];

            if ($Data->personal_email || $Data->official_email) {
                /*if($Data->personal_email)
                        $this->mail($data,$Data->personal_email,'mail');        
                    if($Data->official_email)
                        $this->mail($data,$Data->official_email,'mail'); */
            }
        }



        $set = GeneralSettings::find(1);
        if ($set->email_ids) {
            $emp_info = ['name' => '', 'content' => $info, 'expired_content' => $ex_info];
            foreach (explode(",", $set->email_ids) as $email_Data) {
                $this->mail($emp_info, $email_Data, 'document_expiry_admin');
            }
        }
        //dd($allEmployee);

    }

    function days($from_date, $to_date)
    {
        $date1 = new DateTime($from_date);
        $date2 = new DateTime($to_date);
        $days  = $date2->diff($date1)->format('%a');
        return $days;
    }

    function expire($doc, $date, $Data)
    {
        return ['doc' => $doc, 'date' => DATE('d-m-Y', strtotime($date)), 'days' => $this->days($date, DATE('Y-m-d')), 'employee_name' => $Data->first_name . " " . $Data->last_name];
    }

    function mail($data, $to, $blade)
    {

        $set = GeneralSettings::find(1);
        if ($blade == "document_expiry_admin")
            $subject = $set->employeedoc_mail_admin_subject;
        else
            $subject = $set->employeedoc_mail_subject;

        Mail::send(['html' => $blade], $data, function ($message) use ($to, $data, $set, $subject) {
            $message->to($to, $data['name'])->subject($subject);
            $message->from($set->employeedoc_sender_mail, $set->employeedoc_sender_name);
        });
    }
}
