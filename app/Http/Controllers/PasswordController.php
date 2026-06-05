<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('profile.change_password');
    }

    public function update(Request $request)
    {
        $data = $request->only(['password', 'password_confirmation']);

        $validator = Validator::make($data, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $user->password = Hash::make($data['password']);
        $user->save();

        return Redirect::back()->with('success', 'Password updated successfully.');
    }
}
