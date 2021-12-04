<?php

namespace Transfer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Money;
use User\User;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAmount(): Money
    {
        return Money::BRL($this->getAttribute('amount'));
    }
}
