<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer represents the registered user that owns a wallet.
 */
class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Assignable attributes.
     */
    protected $fillable = [
        'document',
        'names',
        'email',
        'phone',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Relationship: One customer has one wallet.
     *
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Scope: Find a customer by document and phone.
     *
     * This scope is used for wallet actions that require both values.
     *
     * @param Builder $query
     * @param string $document
     * @param string $phone
     * @return Builder
     */
    public function scopeByDocumentAndPhone(Builder $query, string $document, string $phone): Builder
    {
        return $query->where('document', $document)->where('phone', $phone);
    }

    /**
     * Automatically create a wallet for each customer on creation.
     *
     * This guarantees the one-to-one invariant between customer and wallet.
     */
    protected static function booted(): void
    {
        static::created(function (Customer $customer): void {
            $customer->wallet()->create([
                'balance' => 0.00,
            ]);
        });
    }
}
