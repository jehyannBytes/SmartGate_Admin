<?php
// app/Models/AdminUser.php
// SmartGate ACC — Admin User Model (HR and ICTMO accounts)

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table      = 'admin_users';
    protected $primaryKey = 'admin_id';   // ← IMPORTANT: not 'id'

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ── Role Helpers ──────────────────────────────────────────────────
    public function isHR(): bool
    {
        return $this->role === 'hr';
    }

    public function isICTMO(): bool
    {
        return $this->role === 'ictmo';
    }

    // ── Relationships ─────────────────────────────────────────────────
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'admin_id');
    }

    public function monthlyReports()
    {
        return $this->hasMany(\App\Models\MonthlyReport::class, 'generated_by');
    }

    public function approvedLeaveRequests()
    {
        return $this->hasMany(\App\Models\LeaveRequest::class, 'approved_by');
    }

    public function approvedAttRecords()
    {
        return $this->hasMany(\App\Models\AttRecord::class, 'approved_by');
    }

    public function approvedLocatorSlips()
    {
        return $this->hasMany(\App\Models\LocatorSlip::class, 'approved_by');
    }
}
