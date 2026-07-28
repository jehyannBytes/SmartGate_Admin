<?php
namespace Database\Seeders;

use App\Models\Device;
use App\Models\Employee;
use App\Models\AttendanceLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            ["device_name" => "Main Gate Scanner",    "device_mac" => "00:1A:2B:3C:4D:01", "device_type" => "dedicated", "location" => "Main Gate", "last_seen_at" => now()->subMinutes(2)],
            ["device_name" => "Back Gate Scanner",     "device_mac" => "00:1A:2B:3C:4D:02", "device_type" => "dedicated", "location" => "Back Gate", "last_seen_at" => now()->subMinutes(5)],
            ["device_name" => "Admin Building Scanner","device_mac" => "00:1A:2B:3C:4D:03", "device_type" => "dedicated", "location" => "Admin Building", "last_seen_at" => now()->subHours(3)],
            ["device_name" => "Mobile Scanner Unit 1", "device_mac" => "00:1A:2B:3C:4D:04", "device_type" => "mobile",    "location" => null, "last_seen_at" => now()->subDays(2)],
        ];

        $deviceModels = collect($devices)->map(function ($d) {
            return Device::create(array_merge($d, ["is_active" => true]));
        });

        $departments = ["College of Engineering", "College of Business", "Registrar Office", "Facilities"];
        $employmentTypes = ["faculty", "non-teaching"];
        $positions = [
            "College of Engineering" => ["Instructor", "Associate Professor", "Lab Technician"],
            "College of Business"    => ["Instructor", "Department Head"],
            "Registrar Office"       => ["Records Officer", "Clerk"],
            "Facilities"             => ["Maintenance Staff", "Security Guard"],
        ];
        $firstNames = ["Juan", "Maria", "Jose", "Ana", "Pedro", "Carmen", "Luis", "Rosa", "Miguel", "Elena", "Carlos", "Sofia"];
        $lastNames  = ["Santos", "Reyes", "Cruz", "Bautista", "Garcia", "Torres", "Flores", "Ramos", "Mendoza", "Aquino", "Del Rosario", "Villanueva"];

        $employees = collect();
        $counter = 1;

        foreach ($departments as $dept) {
            foreach (range(1, 5) as $i) {
                $employmentType = $employmentTypes[array_rand($employmentTypes)];
                $employee = Employee::create([
                    "employee_code"   => "EMP-" . str_pad($counter, 4, "0", STR_PAD_LEFT),
                    "last_name"       => $lastNames[array_rand($lastNames)],
                    "first_name"      => $firstNames[array_rand($firstNames)],
                    "middle_name"     => null,
                    "department"      => $dept,
                    "employment_type" => $employmentType,
                    "position"        => $positions[$dept][array_rand($positions[$dept])],
                    "photo_url"       => null,
                    "is_active"       => true,
                ]);

                $employees->push($employee);
                $counter++;
            }
        }

        for ($day = 29; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            if ($date->isWeekend()) { continue; }

            foreach ($employees as $employee) {
                if (rand(1, 100) > 85) { continue; }

                $isLate = rand(1, 100) <= 20;
                $inHour = $isLate ? rand(8, 9) : 7;
                $inMinute = $isLate ? rand(1, 59) : rand(30, 59);

                $morningIn = $date->copy()->setTime($inHour, $inMinute);
                AttendanceLog::create([
                    "employee_id"      => $employee->employee_id,
                    "device_id"        => $deviceModels->random()->device_id,
                    "log_type"         => "Morning In",
                    "scanned_at"       => $morningIn,
                    "geofence_passed"  => true,
                    "face_verified"    => true,
                    "sync_status"      => "synced",
                    "local_uuid"       => (string) \Illuminate\Support\Str::uuid(),
                    "synced_at"        => $morningIn,
                ]);

                $morningOut = $date->copy()->setTime(12, rand(0, 15));
                AttendanceLog::create([
                    "employee_id"      => $employee->employee_id,
                    "device_id"        => $deviceModels->random()->device_id,
                    "log_type"         => "Morning Out",
                    "scanned_at"       => $morningOut,
                    "geofence_passed"  => true,
                    "face_verified"    => true,
                    "sync_status"      => "synced",
                    "local_uuid"       => (string) \Illuminate\Support\Str::uuid(),
                    "synced_at"        => $morningOut,
                ]);

                $afternoonIn = $date->copy()->setTime(13, rand(0, 15));
                AttendanceLog::create([
                    "employee_id"      => $employee->employee_id,
                    "device_id"        => $deviceModels->random()->device_id,
                    "log_type"         => "Afternoon In",
                    "scanned_at"       => $afternoonIn,
                    "geofence_passed"  => true,
                    "face_verified"    => true,
                    "sync_status"      => "synced",
                    "local_uuid"       => (string) \Illuminate\Support\Str::uuid(),
                    "synced_at"        => $afternoonIn,
                ]);

                $afternoonOut = $date->copy()->setTime(17, rand(0, 30));
                AttendanceLog::create([
                    "employee_id"      => $employee->employee_id,
                    "device_id"        => $deviceModels->random()->device_id,
                    "log_type"         => "Afternoon Out",
                    "scanned_at"       => $afternoonOut,
                    "geofence_passed"  => true,
                    "face_verified"    => true,
                    "sync_status"      => "synced",
                    "local_uuid"       => (string) \Illuminate\Support\Str::uuid(),
                    "synced_at"        => $afternoonOut,
                ]);
            }
        }

        $this->command->info("Sample data seeded: " . $deviceModels->count() . " devices, " . $employees->count() . " employees, and attendance logs.");
    }
}