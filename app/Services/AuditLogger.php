<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $action, ?object $subject = null, ?string $description = null, ?array $changes = null): void
    {
        $subjectType  = $subject ? class_basename($subject) : null;
        $subjectId    = null;
        $subjectLabel = null;

        if ($subject) {
            foreach (['employee_id', 'device_id', 'admin_id', 'leave_id', 'slip_id', 'att_id', 'report_id', 'notif_id', 'qr_id', 'log_id', 'id'] as $key) {
                if (isset($subject->$key)) {
                    $subjectId = $subject->$key;
                    break;
                }
            }

            if (isset($subject->full_name)) {
                $subjectLabel = $subject->full_name;
            } elseif (isset($subject->first_name, $subject->last_name)) {
                $subjectLabel = "{$subject->last_name}, {$subject->first_name}";
            } elseif (isset($subject->device_name)) {
                $subjectLabel = $subject->device_name;
            } elseif (isset($subject->username)) {
                $subjectLabel = $subject->username;
            }
        }

        AuditLog::create([
            'admin_id'      => Auth::id(),
            'action'        => $action,
            'subject_type'  => $subjectType,
            'subject_id'    => $subjectId,
            'subject_label' => $subjectLabel,
            'description'   => $description,
            'changes'       => $changes,
            'ip_address'    => Request::ip(),
        ]);
    }
}