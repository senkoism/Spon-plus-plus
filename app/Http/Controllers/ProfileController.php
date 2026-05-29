<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
 
class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', [
            'user' => auth()->user()
        ]);
    }
 
    public function update(Request $request)
    {
        $user = auth()->user();
 
        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'username' => ['required', 'string', 'lowercase', 'max:30', 'regex:/^[a-z0-9_]+$/', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', 'lowercase', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.regex' => 'Full name can only contain letters and spaces.',
            'username.regex' => 'Username can only contain lowercase letters, numbers, and underscores. No spaces allowed.',
            'email.lowercase' => 'Email address must be in lowercase.',
        ]);
 
        $user->update([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
        ]);
 
        return back()->with('success', 'Profile updated successfully.');
    }
 
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);
 
        $user = auth()->user();
 
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }
 
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
 
        return back()->with('success', 'Password updated successfully.');
    }
}
