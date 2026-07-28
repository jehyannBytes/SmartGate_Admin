<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFaceCapture extends Model
{
    protected $primaryKey = 'capture_id';

    protected $fillable = [
        'employee_id',
        'image_path',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
