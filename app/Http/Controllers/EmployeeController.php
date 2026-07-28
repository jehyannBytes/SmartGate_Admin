<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%")
                  ->orWhere('position', 'like', "%$search%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees  = $query->orderBy('last_name')->paginate(15)->withQueryString();
        $departments = Employee::distinct()->pluck('department')->sort()->values();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function show(Employee $employee): View
    {
        $employee->load(['attendanceLogs' => function ($q) {
            $q->latest('scanned_at')->limit(10);
        }]);

        return view('employees.show', compact('employee'));
    }

    public function create(): View
    {
        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_code'   => ['required', 'string', 'max:50', 'unique:employees'],
            'last_name'       => ['required', 'string', 'max:100'],
            'first_name'      => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'department'      => ['required', 'string', 'max:100'],
            'employment_type' => ['required', 'in:faculty,non-teaching'],
            'position'        => ['required', 'string', 'max:100'],
            'photo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        unset($validated['photo']);
        $validated['is_active'] = true;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employee-photos', 'public');
            $validated['photo_url'] = 'storage/' . $path;
        }

        $employee = Employee::create($validated);

        AuditLogger::log('created', $employee, "Registered employee {$employee->last_name}, {$employee->first_name}");

        return redirect()->route('employees.enroll-face', $employee)
                         ->with('success', 'Employee added. Now enroll their face for gate verification.');
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'employee_code'   => ['required', 'string', 'max:50', 'unique:employees,employee_code,' . $employee->employee_id . ',employee_id'],
            'last_name'       => ['required', 'string', 'max:100'],
            'first_name'      => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'department'      => ['required', 'string', 'max:100'],
            'employment_type' => ['required', 'in:faculty,non-teaching'],
            'position'        => ['required', 'string', 'max:100'],
            'photo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'is_active'       => ['boolean'],
        ]);

        unset($validated['photo']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employee-photos', 'public');
            $validated['photo_url'] = 'storage/' . $path;
        }

        $employee->update($validated);

        AuditLogger::log('updated', $employee, "Updated employee {$employee->last_name}, {$employee->first_name}");

        return redirect()->route('employees.show', $employee)
                         ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->update(['is_active' => false]);

        AuditLogger::log('deactivated', $employee, "Deactivated employee {$employee->last_name}, {$employee->first_name}");

        return redirect()->route('employees.index')
                         ->with('success', 'Employee deactivated successfully.');
    }
}