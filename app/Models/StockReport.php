<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReport extends Model
{
    protected $fillable = [
        'batch_id',
        'user_id',
        'reviewed_by',
        'stock_transaction_id',
        'type',
        'quantity',
        'status',
        'notes',
        'reviewer_note',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function stockTransaction(): BelongsTo
    {
        return $this->belongsTo(
            StockTransaction::class,
            'stock_transaction_id'
        );
    }
}