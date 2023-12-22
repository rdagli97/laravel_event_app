<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:11|min:11',
            'image' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $this->saveImage($request->image, 'profiles');

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'image' => $data['image'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::attempt($data)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 403);
        }

        return response()->json([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Success',
        ], 200);
    }
}
