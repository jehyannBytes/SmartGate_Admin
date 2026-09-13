<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = LeaveRequest::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $requests = $query->latest('filed_at')->paginate(15)->withQueryString();
        $pending  = LeaveRequest::where('status', 'pending')->count();
        $approved = LeaveRequest::where('status', 'approved')->count();
        $rejected = LeaveRequest::where('status', 'rejected')->count();

        return view('leave-requests.index', compact('requests', 'pending', 'approved', 'rejected'));
    }

    public function create(): View
    {
        $employees = Employee::where('is_active', true)->orderBy('last_name')->get();
        return view('leave-requests.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,employee_id'],
            'leave_type'  => ['required', 'string'],
            'total_days'  => ['required', 'numeric', 'min:0.5'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'filed_at'    => ['required', 'date'],
            'attachment'  => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('leave_scans', 'public');
            $filePath = Storage::url($path);
        }

        $leave = LeaveRequest::create([
            'employee_id'      => $request->employee_id,
            'leave_type'       => $request->leave_type,
            'details_location' => $request->details_location,
            'details_specify'  => $request->details_specify,
            'total_days'       => $request->total_days,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'commutation'      => $request->commutation ?? 'Not Requested',
            'image_url'        => $filePath,
            'status'           => 'approved',
            'approved_by'      => Auth::id(),
            'filed_at'         => $request->filed_at,
            'approved_at'      => now(),
        ]);

        $employee = Employee::find($request->employee_id);
        $employeeName = $employee->last_name . ', ' . $employee->first_name;
        AuditLogger::log('created_and_approved', $leave, "Uploaded and approved CS Form No. 6 for {$employeeName}");

        return redirect()->route('leave-requests.index')->with('success', 'Leave Request successfully recorded and integrated.');
    }

    public function show(LeaveRequest $leaveRequest): View
    {
        $leaveRequest->load('employee');
        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approved,rejected'],
        ]);

        $leaveRequest->update([
            'status'      => $request->action,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $employeeName = $leaveRequest->employee->last_name . ', ' . $leaveRequest->employee->first_name;
        AuditLogger::log($request->action, $leaveRequest, ucfirst($request->action) . " leave request for {$employeeName}");

        $msg = $request->action === 'approved' ? 'Leave Request approved.' : 'Leave Request rejected.';
        return redirect()->route('leave-requests.index')->with('success', $msg);
    }
}