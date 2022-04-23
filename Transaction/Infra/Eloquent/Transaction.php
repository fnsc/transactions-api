<?php

namespace Transaction\Infra\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Money;

class Transaction extends Model
{
    /**
     * @var string[]
     */
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
