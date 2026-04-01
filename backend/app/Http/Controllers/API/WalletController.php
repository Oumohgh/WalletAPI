<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $wallets = Wallet::where('user_id', auth()->id())->with('user', 'currency')->get();

        return response()->json([
            "success" => true,
            'message' => 'Liste des wallets recupéres',
            'data' =>$wallets
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WalletRequest $request)
    {
        $validateed = $request->validated();
        $validateed['user_id'] = auth()->id();

        $wallet = Wallet::create($validateed);
        $wallet->load('currency');
        return response()->json([
            'success' => true,
            'message' => 'Wallet cree avec succes',
            'data' => $wallet,
        ], 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet  $wallet)
    {
        if ($wallet->user->id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        if(!Wallet::findOrFail($wallet->id)){
            return response()->json([
                'success' => false,
                'message' => "Le wallet  est introuvable",
            ], 404);
        }
        $wallet->load('currency');
        return response()->json([
            "success" => true,
            'message' => 'Detail du wallet recupére',
            'data' => $wallet
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WalletRequest  $request, Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        $validateed = $request->validated();
        //$validateed['user_id'] = auth()->id();

        $wallet->devise_id = $validateed['devise_id'];
        $wallet->balance = $validateed['balance'];
        $wallet->save();
        $wallet->load('currency');
        return response()->json([
            'success' => true,
            'message' => 'Wallet mise a jour  avec succes',
            'data' => $wallet->currency,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        $wallet->delete();
        return response()->json([
            'success' => true,
            'message' => 'Wallet supprimer avec succes',
        ]);
    }
}
