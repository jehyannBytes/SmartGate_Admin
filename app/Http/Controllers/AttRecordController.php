<?php

namespace App\Http\Controllers;

use App\Models\AttRecord;
use App\Models\Employee;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AttRecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = AttRecord::with('employee');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->has('status')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $records  = $query->latest('filed_at')->paginate(15)->withQueryString();
        $pending  = AttRecord::where('status', 'pending')->count();
        $approved = AttRecord::where('status', 'approved')->count();
        $rejected = AttRecord::where('status', 'rejected')->count();

        return view('att-records.index', compact('records', 'pending', 'approved', 'rejected'));
    }

    public function create(): View
    {
        $employees = Employee::where('is_active', true)->orderBy('last_name')->get();
        return view('att-records.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id'     => ['required', 'exists:employees,employee_id'],
            'att_number'      => ['nullable', 'string', 'max:50'],
            'destination'     => ['required', 'string', 'max:255'],
            'purpose'         => ['required', 'string'],
            'departure_date'  => ['required', 'date'],
            'departure_time'  => ['nullable'],
            'arrival_date'    => ['required', 'date', 'after_or_equal:departure_date'],
            'arrival_time'    => ['nullable'],
            'travel_type'     => ['required', 'in:Official Business,Official Time'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'filed_at'        => ['required', 'date'],
            'attachment'      => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('att_scans', 'public');
            $filePath = Storage::url($path);
        }

        $attRecord = AttRecord::create([
            'employee_id'     => $request->employee_id,
            'att_number'      => $request->att_number,
            'destination'     => $request->destination,
            'purpose'         => $request->purpose,
            'departure_date'  => $request->departure_date,
            'departure_time'  => $request->departure_time,
            'arrival_date'    => $request->arrival_date,
            'arrival_time'    => $request->arrival_time,
            'travel_type'     => $request->travel_type,
            'supervisor_name' => $request->supervisor_name,
            'campus_director' => 'LIZA L. QUIMSON, EdD',
            'image_url'       => $filePath,
            'status'          => 'approved',
            'approved_by'     => Auth::id(),
            'filed_at'        => $request->filed_at,
            'approved_at'     => now(),
        ]);

        $employee = Employee::find($request->employee_id);
        $employeeName = $employee->last_name . ', ' . $employee->first_name;
        AuditLogger::log('created_and_approved', $attRecord, "Uploaded and approved ATT form for {$employeeName}");

        return redirect()->route('att-records.index')->with('success', 'ATT record successfully created and integrated into DTR.');
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