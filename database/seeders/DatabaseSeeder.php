<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ParkingRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Parking rates
        ParkingRate::firstOrCreate(['vehicle_type' => 'motor'], ['hourly_rate' => 2000]);
        ParkingRate::firstOrCreate(['vehicle_type' => 'mobil'], ['hourly_rate' => 5000]);

        // Admin
        $admin = User::firstOrCreate(['email' => 'admin@parking.com'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_number' => '081234567890',
            'is_2fa_enabled' => false,
        ]);
        $admin->assignRole($adminRole);

        // Test user
        $user = User::firstOrCreate(['email' => 'user@parking.com'], [
            'name' => 'User Test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($userRole);
    }
}
