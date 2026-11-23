<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'batch_id',
        'user_id',
        'transaction_code',
        'type',
        'quantity',
        'blockchain_tx_hash',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}