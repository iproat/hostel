<?php

namespace App\Http\Controllers\Reminder;

use App\Http\Requests\DesignationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Designation;
use App\Model\Employee;
use App\Model\Reminder;
use App\Model\GeneralSettings;
use Response;


class GeneralSettingsController extends Controller
{

    public function settings(){
        $setting = GeneralSettings::find(1);
        return view('admin.reminder.settings', ['editModeData' => $setting]);
    }

    public function settingsstore(Request $request){

        $request->validate(['email_ids'=>'required']);

        $reminder_settings=GeneralSettings::find(1);
        $reminder_settings->email_ids=$request->email_ids;
        $reminder_settings->employeedoc_mail_subject=$request->employeedoc_mail_subject;
        $reminder_settings->employeedoc_mail_admin_subject=$request->employeedoc_mail_admin_subject;
        $reminder_settings->employeedoc_sender_name=$request->employeedoc_sender_name;        
        $reminder_settings->officedoc_mail_subject=$request->officedoc_mail_subject;        
        $reminder_settings->officedoc_sender_name=$request->officedoc_sender_name;
        $reminder_settings->save();

        return redirect()->back()->with('success', 'Reminder Settings successfully updated.');
    }

}
