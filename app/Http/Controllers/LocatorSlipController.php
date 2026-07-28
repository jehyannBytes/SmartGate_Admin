<?php
namespace App\Http\Controllers;

use App\Models\LocatorSlip;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LocatorSlipController extends Controller
{
    public function index(Request $request): View
    {
        $query = LocatorSlip::with('employee');

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

        $slips    = $query->latest('filed_at')->paginate(15)->withQueryString();
        $pending  = LocatorSlip::where('status', 'pending')->count();
        $approved = LocatorSlip::where('status', 'approved')->count();
        $rejected = LocatorSlip::where('status', 'rejected')->count();

        return view('locator-slips.index', compact('slips', 'pending', 'approved', 'rejected'));
    }

    public function show(LocatorSlip $locatorSlip): View
    {
        $locatorSlip->load('employee');
        return view('locator-slips.show', compact('locatorSlip'));
    }

    public function update(Request $request, LocatorSlip $locatorSlip): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approved,rejected'],
        ]);

        $locatorSlip->update([
            'status'      => $request->action,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $employeeName = $locatorSlip->employee->last_name . ', ' . $locatorSlip->employee->first_name;
        AuditLogger::log($request->action, $locatorSlip, ucfirst($request->action) . " locator slip for {$employeeName}");

        $msg = $request->action === 'approved' ? 'Locator Slip approved.' : 'Locator Slip rejected.';
        return redirect()->route('locator-slips.index')->with('success', $msg);
    }
}