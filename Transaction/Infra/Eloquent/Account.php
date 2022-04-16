<?php

namespace Transaction\Infra\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Money;

class Account extends Model
{
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
