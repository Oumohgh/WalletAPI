<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{

    protected $fillable = ['amount', 'type', 'wallet_id','description', 'receiver_wallet_id', 'sender_wallet_id', 'balance_after'];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];


    public function wallet() : BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaction_out() : BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }
    public function transaction_in() : BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }
}
