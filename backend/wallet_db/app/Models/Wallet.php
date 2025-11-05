<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Wallet holds the monetary balance for a customer.
 */
class Wallet extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Assignable attributes.
     */
    protected $fillable = [
        'balance',
        'customer_id',
    ];

    /**
     * Attributes casting.
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'balance' => 'decimal:2',
    ];

    /**
     * Relationship: The wallet belongs to a customer.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship: A wallet has many transactions.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relationship: A wallet has many payment sessions.
     *
     * @return HasMany
     */
    public function paymentSessions(): HasMany
    {
        return $this->hasMany(PaymentSession::class);
    }

    /**
     * Credit the wallet by a positive amount and create a transaction record.
     *
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     */
    public function credit(float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0.0) {
            throw new \InvalidArgumentException('Credit amount must be positive.');
        }

        $this->balance = (float) $this->balance + $amount;

        return $this->transactions()->create([
            'type' => Transaction::TYPE_CREDIT,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Debit the wallet by a positive amount if sufficient balance is available.
     * Records a transaction of type DEBIT.
     *
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     */
    public function debit(float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0.0) {
            throw new \InvalidArgumentException('Debit amount must be positive.');
        }

        if ((float) $this->balance < $amount) {
            throw new \RuntimeException('Insufficient balance for debit.');
        }

        $this->balance = (float) $this->balance - $amount;

        return $this->transactions()->create([
            'type' => Transaction::TYPE_DEBIT,
            'amount' => $amount,
            'description' => $description,
        ]);
    }
}
