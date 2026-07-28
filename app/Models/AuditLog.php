<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'audit_id';

    public $timestamps = true;

    protected $fillable = [
        'admin_id', 'action', 'subject_type', 'subject_id',
        'subject_label', 'description', 'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id', 'admin_id');
    }
}