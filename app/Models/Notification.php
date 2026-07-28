<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notif_id';

    protected $fillable = [
        'admin_id', 'notif_type', 'message', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id', 'admin_id');
    }
}
