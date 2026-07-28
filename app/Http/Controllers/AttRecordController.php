<?php
namespace App\Http\Controllers;

use App\Models\AttRecord;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AttRecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = AttRecord::with('employee');

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

        $records  = $query->latest('filed_at')->paginate(15)->withQueryString();
        $pending  = AttRecord::where('status', 'pending')->count();
        $approved = AttRecord::where('status', 'approved')->count();
        $rejected = AttRecord::where('status', 'rejected')->count();

        return view('att-records.index', compact('records', 'pending', 'approved', 'rejected'));
    }

    public function show(AttRecord $attRecord): View
    {
        $attRecord->load('employee', 'approvedBy');
        return view('att-records.show', compact('attRecord'));
    }

    public function update(Request $request, AttRecord $attRecord): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approved,rejected'],
        ]);

        $attRecord->update([
            'status'      => $request->action,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $employeeName = $attRecord->employee->last_name . ', ' . $attRecord->employee->first_name;
        AuditLogger::log($request->action, $attRecord, ucfirst($request->action) . " ATT record for {$employeeName}");

        $msg = $request->action === 'approved' ? 'ATT Record approved.' : 'ATT Record rejected.';
        return redirect()->route('att-records.index')->with('success', $msg);
    }
}