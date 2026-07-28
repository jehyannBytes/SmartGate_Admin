<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LocatorSlip extends Model
{
    protected $table = 'locator_slips';
    protected $primaryKey = 'slip_id';

    protected $fillable = [
        'employee_id', 'approved_by', 'image_url',
        'location', 'purpose', 'start_time',
        'end_time', 'status', 'filed_at', 'approved_at',
    ];

    protected $casts = [
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
        'filed_at'    => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
