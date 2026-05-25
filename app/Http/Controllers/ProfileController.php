<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Show profile edit form
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    // Update name and email
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email',
        ]);

        auth()->user()->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    // Delete account
    public function destroy(Request $request)
    {
        $user = auth()->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        return redirect('/');
    }
}
