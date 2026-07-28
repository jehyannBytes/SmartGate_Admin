<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';
    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'employee_id', 'approved_by', 'image_url',
        'leave_type', 'start_date', 'end_date',
        'total_days', 'status', 'filed_at', 'approved_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'filed_at'    => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
