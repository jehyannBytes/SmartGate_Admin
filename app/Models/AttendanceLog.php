<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'employee_id', 'device_id', 'log_type',
        'scanned_at', 'geofence_passed', 'face_verified',
        'sync_status', 'local_uuid', 'synced_at',
    ];

    protected $casts = [
        'scanned_at'      => 'datetime',
        'synced_at'       => 'datetime',
        'geofence_passed' => 'boolean',
        'face_verified'   => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}
