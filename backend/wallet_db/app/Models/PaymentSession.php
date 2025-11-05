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
     * Assignable attributes.
     */
    protected $fillable = [
        'token',
        'confirmed',
        'amount',
        'wallet_id',
    ];

    /**
     * Attributes casting.
     */
    protected $casts = [
        'id' => 'integer',
        'wallet_id' => 'integer',
        'confirmed' => 'boolean',
        'amount' => 'decimal:2',
    ];

    /**
     * The payment session belongs to a wallet.
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Check if the provided token matches the session token.
     *
     * @param string $token
     * @return bool
     */
    public function matchesToken(string $token): bool
    {
        return $this->token === $token;
    }

    /**
     * Mark the session as confirmed.
     * 
     * @return void
     */
    public function confirm(): void
    {
        $this->confirmed = true;
    }
}
