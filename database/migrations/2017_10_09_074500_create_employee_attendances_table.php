<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->increments('employee_attendance_id');
            $table->string('finger_print_id', 50);
            $table->integer('employee_id');
            $table->integer('work_shift_id');
            $table->dateTime('in_out_time');
            $table->string('status', 50);
            $table->tinyInteger('inout_status');
            $table->text('check_type')->nullable();
            $table->bigInteger('verify_code')->nullable();
            $table->text('sensor_id')->nullable();
            $table->text('Memoinfo')->nullable();
            $table->text('WorkCode')->nullable();
            $table->text('sn')->nullable();
            $table->integer('UserExtFmt')->nullable();
            $table->string('mechine_sl', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_attendance');
    }
}
