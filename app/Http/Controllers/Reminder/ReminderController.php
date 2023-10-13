<?php

namespace App\Http\Controllers\Reminder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Reminder;
use Response;



class ReminderController extends Controller
{

    public function index(){
        $results = Reminder::where('status',1)->where('expiry_date','>=',DATE('Y-m-d'))->get();
        return view('admin.reminder.index',['results'=>$results]);
    }
    
    public function expired(){
        $results = Reminder::where('status',1)->where('expiry_date','<',DATE('Y-m-d'))->get();
        return view('admin.reminder.expired',['results'=>$results]);
    }


    public function create(){
        return view('admin.reminder.form');
    }


    public function store(Request $request){

        $request->validate([
            'title'=>'required',
            'file'=>'required|file|mimes:jpeg,jpg,png,pdf',//,doc,docx
            'expiry_date'=>'required',
            'content'=>'required'

        ]);
        $input = $request->all();
        $input['expiry_date']=DATE('Y-m-d',strtotime($request->expiry_date));
        $file = $request->file('file');

        if($file){
            $fileName = md5(str_random(30) . time() . '_' . $request->file('file')) . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->move('uploads/officeManagement/', $fileName);
            $input['file'] = $fileName;
        }

        try{
            Reminder::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug =1;
        }

        if($bug==0){
            return redirect('OfficeManagement')->with('success', 'Office Management Successfully saved.');
        }else {
            return redirect('OfficeManagement')->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function edit($id){
        $editModeData = Reminder::findOrFail($id);
        if($editModeData->expiry_date !="0000-00-00")
            $editModeData->expiry_date=DATE('d-m-Y',strtotime($editModeData->expiry_date));
        return view('admin.reminder.form',['editModeData' => $editModeData]);
    }


    public function update(Request $request,$id) {

        
        $request->validate([
            'title'=>'required',
            'file'=>'file|mimes:jpeg,jpg,png,pdf',//,doc,docx
            'expiry_date'=>'required',
            'content'=>'required'

        ]);
        $file = $request->file('file');

        $data = Reminder::findOrFail($id);
        
        $input = $request->all();
        $input['expiry_date']=DATE('Y-m-d',strtotime($request->expiry_date));



        if($file){

            \unlink('uploads/officeManagement/'.$data->file);

            $fileName = md5(str_random(30) . time() . '_' . $request->file('file')) . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->move('uploads/officeManagement/', $fileName);
            $input['file'] = $fileName;
        }

        try{
            $data->update($input);
            //dd($request->expiry_date);
            //dd($data->expiry_date);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug =1;
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Office Management Successfully updated.');
        }else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function destroy($id){
         
        try{
            $department = Reminder::FindOrFail($id);
            $department->status=2;
            $department->update();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug =1;
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function download(Request $request){

        $filepath = 'uploads/officeManagement/'.$request->file;
        return Response::download($filepath); 
    }

   

}
