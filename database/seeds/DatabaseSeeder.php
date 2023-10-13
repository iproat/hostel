<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ModuleSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(DesignationSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(MenuPermission::class);
        // $this->call(EmployeeAttendanceSeeder::class);
        $this->call(TaxRuleSeeder::class);
        $this->call(SalaryDeductionForLateSeeder::class);
        $this->call(LeaveTypeSeeder::class);
        $this->call(EarnLeaveRuleSeeder::class);
        $this->call(FontSettingSeeder::class);
        $this->call(IpSettingSeeder::class);
        $this->call(PaidLeaveRuleSeeder::class);
        $this->call(FoodDeductionRuleSeeder::class);
        $this->call(TelephoneDeductionRuleSeeder::class);
        $this->call(OvertimeRuleConfigSeeder::class);

        //log
        // $this->call(MsSqlLog::class);

        //user and employee
        // $this->call(UserSeeder::class);

    }
}
