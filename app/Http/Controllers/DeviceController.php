<?php
namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class DeviceController extends Controller
{
    public function index(): View
    {
        $devices = Device::orderBy('device_name')->get()->map(function ($device) {
            $device->is_online = $device->last_seen_at &&
                Carbon::parse($device->last_seen_at)->diffInMinutes(now()) < 10;
            return $device;
        });

        $total   = $devices->count();
        $online  = $devices->where('is_online', true)->count();
        $offline = $devices->where('is_online', false)->where('is_active', true)->count();

        return view('devices.index', compact('devices', 'total', 'online', 'offline'));
    }

    public function create(): View
    {
        return view('devices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:100'],
            'device_mac'  => ['required', 'string', 'max:17', 'unique:devices,device_mac',
                              'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            'device_type' => ['required', 'in:dedicated,mobile'],
            'location'    => ['nullable', 'string', 'max:100'],
        ]);

        $validated['is_active'] = true;
        $device = Device::create($validated);

        AuditLogger::log('created', $device, "Registered device {$device->device_name}");

        return redirect()->route('devices.index')
                          ->with('success', 'Device registered successfully.');
    }

    public function edit(Device $device): View
    {
        $device->is_online = $device->last_seen_at &&
            Carbon::parse($device->last_seen_at)->diffInMinutes(now()) < 10;

        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:100'],
            'device_mac'  => ['required', 'string', 'max:17',
                              'unique:devices,device_mac,' . $device->device_id . ',device_id',
                              'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            'device_type' => ['required', 'in:dedicated,mobile'],
            'location'    => ['nullable', 'string', 'max:100'],
            'is_active'   => ['boolean'],
        ]);

        $device->update($validated);

        AuditLogger::log('updated', $device, "Updated device {$device->device_name}");

        return redirect()->route('devices.index')
                          ->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->update(['is_active' => false]);

        AuditLogger::log('deactivated', $device, "Deactivated device {$device->device_name}");

        return redirect()->route('devices.index')
                          ->with('success', 'Device deactivated.');
    }

    public function show(Device $device): View
    {
        $device->is_online = $device->last_seen_at &&
            Carbon::parse($device->last_seen_at)->diffInMinutes(now()) < 10;

        $recentLogs = \App\Models\AttendanceLog::with('employee')
            ->where('device_id', $device->device_id)
            ->latest('scanned_at')
            ->limit(10)
            ->get();

        return view('devices.show', compact('device', 'recentLogs'));
    }
}