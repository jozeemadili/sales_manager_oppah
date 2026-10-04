<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Local test data for the driver Android app (mobile/driver_app):
 * a test driver with a truck. Never runs in production.
 *
 *   php artisan db:seed --class=DriverAppTestSeeder
 *   Login: driver.test@oppah.local / Driver-Test-2026
 */
class DriverAppTestSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment('production')) {
            $this->command->error('Refusing to create a test driver in production.');
            return;
        }

        $userId = DB::table('users')->where('email', 'driver.test@oppah.local')->value('id')
            ?? DB::table('users')->insertGetId([
                'first_name' => 'Test', 'last_name' => 'Driver', 'email' => 'driver.test@oppah.local',
                'password' => Hash::make('Driver-Test-2026'), 'role' => 'Driver', 'status' => 'Active',
                'company_id' => 1, 'mobile' => '699999901', 'created_at' => now(),
            ]);

        if (!DB::table('our_trucks')->where('plate_no', 'T000TEST')->exists()) {
            DB::table('our_trucks')->insert(['plate_no' => 'T000TEST', 'driver_id' => $userId, 'created_at' => now(), 'created_by' => 1]);
        }

        if (!DB::table('expenses')->where('to_be_used', 'GARI')->where('status', 'Active')->exists()) {
            DB::table('expenses')->insert(['e_name' => 'CHAKULA SAFARI', 'status' => 'Active', 'company_id' => 1, 'to_be_used' => 'GARI', 'reg_by' => 1, 'reg_date' => now()]);
        }
    }
}
