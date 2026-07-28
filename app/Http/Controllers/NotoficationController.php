<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() { return view('notifications.index', ['notifications' => collect()]); }
    public function markRead($id) { return back()->with('success', 'Marked as read.'); }
    public function markAllRead() { return back()->with('success', 'All marked as read.'); }
}
