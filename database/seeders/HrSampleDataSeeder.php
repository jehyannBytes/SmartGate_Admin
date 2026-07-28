<?php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\AttRecord;
use App\Models\LocatorSlip;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HrSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->inRandomOrder()->limit(6)->get();

        if ($employees->count() < 3) {
            $this->command->warn('Not enough employees found. Run SampleDataSeeder first.');
            return;
        }

        $leaveTypes = ['Vacation Leave', 'Sick Leave', 'Emergency Leave'];

        foreach ($employees->take(4) as $i => $employee) {
            $start = Carbon::now()->addDays(rand(3, 20));
            $end   = $start->copy()->addDays(rand(0, 3));

            LeaveRequest::create([
                'employee_id' => $employee->employee_id,
                'approved_by' => null,
                'image_url'   => null,
                'leave_type'  => $leaveTypes[array_rand($leaveTypes)],
                'start_date'  => $start->toDateString(),
                'end_date'    => $end->toDateString(),
                'total_days'  => $start->diffInDays($end) + 1,
                'status'      => 'pending',
                'filed_at'    => Carbon::now()->subDays(rand(0, 5)),
                'approved_at' => null,
            ]);
        }

        foreach ($employees->take(4) as $employee) {
            AttRecord::create([
                'employee_id' => $employee->employee_id,
                'approved_by' => null,
                'image_url'   => null,
                'att_date'    => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                'reason'      => 'Forgot to scan due to device malfunction',
                'status'      => 'pending',
                'filed_at'    => Carbon::now()->subDays(rand(0, 3)),
                'approved_at' => null,
            ]);
        }

        $locations = ['DepEd Regional Office', 'City Hall', 'Bank - Payroll Errand', 'Supplier Meeting'];
        $purposes  = ['Document submission', 'Official errand', 'Meeting with partner agency'];

        foreach ($employees->take(4) as $employee) {
            $start = Carbon::now()->subDays(rand(0, 2))->setTime(9, 0);

            LocatorSlip::create([
                'employee_id' => $employee->employee_id,
                'approved_by' => null,
                'image_url'   => null,
                'location'    => $locations[array_rand($locations)],
                'purpose'     => $purposes[array_rand($purposes)],
                'start_time'  => $start,
                'end_time'    => $start->copy()->addHours(rand(2, 5)),
                'status'      => 'pending',
                'filed_at'    => $start->copy()->subHours(1),
                'approved_at' => null,
            ]);
        }

        $this->command->info('HR sample data seeded.');
    }
}