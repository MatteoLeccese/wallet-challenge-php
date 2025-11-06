<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PaymentSession represents a pending payment that requires token confirmation.
 */
class PaymentSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Payment session status options.
     */
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';

    /**
     * Assignable attributes.
     */
    protected $fillable = [
        'token',
        'status',
        'amount',
        'from_wallet_id',
        'to_wallet_id',
        'expires_at',
    ];

    /**
     * Attributes casting.
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: The wallet that sends the payment.
     *
     * @return BelongsTo
     */
    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    /**
     * Relationship: The wallet that receives the payment.
     *
     * @return BelongsTo
     */
    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }
}
