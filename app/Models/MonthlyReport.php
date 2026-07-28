<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $table = 'monthly_reports';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'generated_by', 'year', 'month',
        'department', 'file_url', 'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(AdminUser::class, 'generated_by', 'admin_id');
    }
}
