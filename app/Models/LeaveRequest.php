<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LeaveRequest extends Model
{
    public function generateReport(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $year = $request->input('year');
        $month = $request->input('month');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $approvedLeaves = self::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->get();

        $leaveDates = [];
        foreach ($approvedLeaves as $leave) {
            $current = Carbon::parse($leave->start_date)->max($startDate);
            $last = Carbon::parse($leave->end_date)->min($endDate);

            while ($current->lte($last)) {
                $leaveDates[$current->toDateString()] = $leave->leave_type;
                $current->addDay();
            }
        }

        return view('reports.dtr', compact('leaveDates', 'startDate', 'endDate'));
    }
}