<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(RegisterRequest  $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);
        $token = $user->createToken('my-app-token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Inscription reussie',
            'data' => [
                'user' => $user,
                'token' =>  $token,
            ]
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if(Auth::attempt($validated)){
            $user = $request->user();
            $token = $user->createToken('my-app-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Connexion reussie',
                'data' => [
                    'user' => $user,
                    'token' => $token
                    ]
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Identifiants incorrects',
        ], 401);
    }


    /**
     * Display the specified resource.
     */
    public function logout(Request  $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => "Deconnexion reusiie"
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function user(Request  $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'message' => 'Profile utilisateur recupere',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
