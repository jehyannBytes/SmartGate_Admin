<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qr_codes';
    protected $primaryKey = 'qr_id';

    protected $fillable = [
        'employee_id', 'qr_token',
        'issued_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'issued_at'  => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
