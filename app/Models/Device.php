<?php
// app/Models/Device.php
// SmartGate ACC — Device Model

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'devices';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'device_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_name',
        'device_mac',
        'device_type',
        'location',
        'is_active',
        'last_seen_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active'    => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Relationship: attendance logs recorded through this device.
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'device_id', 'device_id');
    }
}
