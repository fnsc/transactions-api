<?php

namespace Transfer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Money;
use User\User;

class Transaction extends Model
{
    protected $fillable = [
        'number',
        'payee_id',
        'payer_id',
        'amount',
    ];

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id', 'id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }

    public function getAmount(): Money
    {
        return Money::BRL($this->getAttribute('amount'));
    }
}
