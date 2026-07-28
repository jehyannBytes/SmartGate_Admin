<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('admin');

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject_label', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $logs = $query->latest('created_at')->paginate(25)->withQueryString();

        $admins       = AdminUser::orderBy('username')->get(['admin_id', 'username', 'full_name']);
        $actionTypes  = AuditLog::distinct()->pluck('action')->sort()->values();
        $subjectTypes = AuditLog::distinct()->pluck('subject_type')->filter()->sort()->values();

        return view('audit-logs.index', compact('logs', 'admins', 'actionTypes', 'subjectTypes'));
    }
}