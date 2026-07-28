<?php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
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