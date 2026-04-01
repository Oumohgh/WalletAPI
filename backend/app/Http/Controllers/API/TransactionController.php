<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use http\Env\Response;
use Illuminate\Http\Request;
use Mockery\Exception;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function transactions(Request  $request,Wallet  $wallet)
    {
        if(auth()->id() !== $wallet->user->id){
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        $perPages = $request->query('per_page', 15);

        $transactions = $wallet->transactions()
            ->select([
                'id',
                'wallet_id',
                'type',
                'amount',
                'description',
                'receiver_wallet_id',
                'sender_wallet_id',
                'balance_after',
                'created_at'
            ])
            ->latest()
            ->paginate($perPages);

        return response()->json([
            'success' => true,
            'message' => 'Historique des transactions recupere',
            'data' => ['transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]]
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function deposit(TransactionRequest $request, Wallet  $wallet)
    {
        if(auth()->id() !== $wallet->user->id){
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        $validated = $request->validated();
        $validated['wallet_id'] = $wallet->id;
        $validated['type'] = 'deposit';
        $transaction = Transaction::create($validated);
        $transaction->balance_after =$wallet->balance + $transaction->amount;
        $wallet->balance = $transaction->balance_after;
        $wallet->save();
        $transaction->save();
        $transaction->load('wallet');

        return response()->json([
            'success' => true,
            'message' => 'Depot effecture avec success',
            'data' => $transaction
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function withdraw(TransactionRequest $request, Wallet  $wallet)
    {
        if(auth()->id() !== $wallet->user->id){
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }
        $validated = $request->validated();
        $validated['wallet_id'] = $wallet->id;
        $validated['type'] = 'withdraw';
        //if wallet amount < amount transaction
        $transaction = Transaction::create($validated);
        $transaction->balance_after = $wallet->balance - $transaction->amount;
        $wallet->balance = $transaction->balance_after;
        $wallet->save();
        $transaction->save();
        $transaction->load('wallet');

        return response()->json([
            'success' => true,
            'message' => 'Retrait effectué avec succès.',
            'data' => $transaction
        ], 200);
    }

    public function transfer(TransactionRequest $request, Wallet  $wallet)
    {
        if(auth()->id() !== $wallet->user->id){
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action."
            ], 403);
        }

        $validated = $request->validated();
        if($wallet->balance < $validated['amount']){
            return response()->json([
                'success' => false,
                'message' => "Solde insuffisant.Solde actuelle : $wallet->balance",
            ], 400);
        }
        $validated['wallet_id'] = $wallet->id;
        try {
            $receiverWallet = Wallet::findOrFail($validated['receiver_wallet_id']);
        }catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => "Le wallet destinataire est introuvable",
            ], 404);
        }
        if($wallet->currency->nom !== $receiverWallet->currency->nom){
            return response()->json([
                'success' => false,
                'message' => "Transfert impossible : les deux wallets doivent avoir la même devise.",
            ], 400);
        }
        $wallet->balance -= $validated['amount'];
        $receiverWallet->balance += $validated['amount'];

        $wallet->save();
        $receiverWallet->save();



        $transaction_out = Transaction::create([
            'wallet_id' => $validated['wallet_id'],
            'type' => 'transfer_out',
            "amount" => $validated['amount'],
            'description' => $validated['description'],
            'receiver_wallet_id' => $receiverWallet->id,
            'balance_after' =>  $wallet->balance,
        ]);
        $transaction_in = Transaction::create([
            'wallet_id' => $receiverWallet->id,
            'type' => 'transfer_in',
            "amount" => $validated['amount'],
            'description' => $validated['description'],
            'sender_wallet_id' =>$wallet->id,
            'balance_after' =>  $receiverWallet->balance,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transfert effectué avec succès.',
            'data' => [
                "transaction_out" => $transaction_out,
                "transaction_in" => $transaction_in,
                'wallet' => $wallet
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

}
