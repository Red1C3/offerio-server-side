<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * signs up and return a valid token
     * 
     * @param Request $request
     * @return array
     */
    public function signup(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
        $token = $user->createToken('offeriosToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    /**
     * Returns a valid token
     * 
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'msg' => 'Invalid creditionals'
            ], 401);
        }
        $token = $user->createToken('offeriosToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    /**
     * Deletes token from server
     * 
     * @param Request $request
     * @return string
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(
            ['msg' => 'Logged Out'],
            200
        );
    }
    public function profile()
    {
        $user = auth()->user();
        $user['products'] = $user->products;
        $user['comments'] = $user->comments;
        return $user;
    }
}
