<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_code', 'last_name', 'first_name', 'middle_name',
        'department', 'employment_type', 'position',
        'photo_url', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id', 'employee_id');
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class, 'employee_id', 'employee_id');
    }

    public function faceCaptures()
    {
        return $this->hasMany(EmployeeFaceCapture::class, 'employee_id', 'employee_id');
    }
}
