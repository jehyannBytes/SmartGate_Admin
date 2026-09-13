<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttRecord extends Model
{
    protected $table = 'att_records';
    protected $primaryKey = 'att_id';

    protected $fillable = [
        'att_number',
        'employee_id',
        'destination',
        'purpose',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
        'travel_type',
        'supervisor_name',
        'campus_director',
        'approved_by',
        'image_url',
        'status',
        'filed_at',
        'approved_at',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date'   => 'date',
        'filed_at'       => 'date',
        'approved_at'   => 'datetime',
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