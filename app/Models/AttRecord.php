<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttRecord extends Model
{
    protected $table = 'att_records';
    protected $primaryKey = 'att_id';

    protected $fillable = [
        'employee_id', 'approved_by', 'image_url',
        'destination', 'purpose', 'travel_date',
        'return_date', 'status', 'filed_at', 'approved_at',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'filed_at'    => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by', 'admin_id');
    }
}
