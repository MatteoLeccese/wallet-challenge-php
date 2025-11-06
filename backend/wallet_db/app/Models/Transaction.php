<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Transaction is an immutable ledger entry associated with a wallet.
 */
class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Allowed transaction types constants.
     */
    public const TYPE_TOP_UP   = 'TOP_UP';
    public const TYPE_PURCHASE = 'PURCHASE';
    public const TYPE_PAYMENT  = 'PAYMENT';
    public const TYPE_DEBIT    = 'DEBIT';
    public const TYPE_CREDIT   = 'CREDIT';
    public const TYPE_TRANSFER = 'TRANSFER';

    /**
     * Assignable attributes.
     */
    protected $fillable = [
        'type',
        'amount',
        'wallet_id',
    ];

    /**
     * Attributes casting.
     */
    protected $casts = [
        'id' => 'integer',
        'wallet_id' => 'integer',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: The transaction belongs to a wallet.
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
