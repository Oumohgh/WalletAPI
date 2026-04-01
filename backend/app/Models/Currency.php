<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'deleted_at'];
//    protected $casts = [
//        'deleted_at' => 'datetime:Y-m-d',
//        'created_at' => 'datetime:Y-m-d',
//        'updated_at' => 'datetime:Y-m-d',
//    ];
//
//    protected $dateFormat = 'U';
//    protected function serializeDate(DateTimeInterface $date): string
//    {
//        return $date->format("Y-m-d H:i:s");
//    }


    public function wallets() : HasMany
    {
        return $this->hasMany(Wallet::class);
    }
}
