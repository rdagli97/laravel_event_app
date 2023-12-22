<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // update user
    public function updateUser(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        return response()->json([
            'image' => $image,
            'message' => 'profile photo changed successfully',
        ], 200);
    }
}
