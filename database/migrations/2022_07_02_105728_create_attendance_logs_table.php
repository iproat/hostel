<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_sql', function (Blueprint $table) {
            $table->increments('primary_id');
            $table->string('primary_id', 50);
            $table->string('devuid', 255);
            $table->string('device_name', 100);
            $table->type('type', 50);
            $table->dateTime('datetime');
            $table->tinyInteger('status');
            $table->integer('employee');
            $table->integer('device');
            $table->string('device_employee_id', 50);
            $table->text('sms_log');
            $table->tinyInteger('live_status');
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
        Schema::dropIfExists('ms_sql');
    }
}
