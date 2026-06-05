<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffOtpMail;

class StaffController extends Controller
{
    // Show staff list and creation form
    public function index()
    {
        $staff = User::where('role', 'staff')->orderBy('name')->get();
        return view('admin.manage_staff', compact('staff'));
    }

    // Create a new staff member (admin only)
    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $email = $data['email'];
        if (! Cache::get("staff_otp_verified:{$email}")) {
            return Redirect::back()->with('error', 'Please verify the OTP sent to the staff email before creating the account.')->withInput();
        }

        Cache::forget("staff_otp:{$email}");
        Cache::forget("staff_otp_verified:{$email}");

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'role' => 'staff',
        ]);

        return Redirect::back()->with('success', 'Staff member created successfully.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $email = $request->email;
        $otp = rand(100000, 999999);

        Cache::put("staff_otp:{$email}", $otp, now()->addSeconds(40));
        Cache::forget("staff_otp_verified:{$email}");

        try {
            Mail::to($email)->send(new StaffOtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Check mail configuration.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully. It is valid for 40 seconds.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = $request->email;
        $storedOtp = Cache::get("staff_otp:{$email}");

        if (! $storedOtp || $storedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP is invalid or has expired. Please request a new one.',
            ], 422);
        }

        Cache::put("staff_otp_verified:{$email}", true, now()->addSeconds(40));

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. You can now set the staff password.',
        ]);
    }

    // Delete a staff member (admin only)
    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user || $user->role !== 'staff') {
            return redirect()->back()->with('error', 'Staff member not found or cannot be deleted.');
        }

        // Prevent deleting yourself
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Staff member deleted.');
    }
}
