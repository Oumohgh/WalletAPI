<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;
    protected $fillable = ['balance', 'name', 'currency_id', 'deleted_at', 'user_id'];

    protected $casts = [
        'balance' => 'decimal:2',
        ];


    public function currency() : BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}
