<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Device;
use App\Http\Controllers\Employee\DeviceController;

class ImportDeviceEmployee extends Command
{
    protected $signature = 'import:deviceemployee';
    protected $name      = "importDeviceEmployee";

    protected $description = 'Import Device Log';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
	
	return true;

        set_time_limit(0);
        Device::where('status',1)->orderBy('id', 'ASC')->chunk(1, function ($Device){
            foreach($Device as $key => $data) {
                $controller = new DeviceController();
                $request=new \Illuminate\Http\Request();
                $request->device=$data->id;
                $controller->importemployee($request);
            }
            
        });

    }
}
