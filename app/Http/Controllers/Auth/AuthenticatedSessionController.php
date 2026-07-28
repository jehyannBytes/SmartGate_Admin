<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php
// SmartGate ACC — Handles admin login and logout using username field

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login form submission.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login using 'username' field
        if (!Auth::attempt(
            [
                'username'  => $request->username,
                'password'  => $request->password,
                'is_active' => true,
            ],
            false // no "remember me"
        )) {
            return back()->withErrors([
                'username' => 'Invalid username or password.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();

        // Redirect to dashboard based on role
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout the admin user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
