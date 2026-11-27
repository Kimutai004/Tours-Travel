<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'destination_id',
        'payment_method',
        'payment_id',
        'user_email',
        'phone',
        'amount',
        'currency',
        'status',
        'transaction_type',
        'mpesa_transaction_id',
        'mpesa_callback_url',
        'stripe_token',
        'stripe_charge_id',
        'notes',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destinations::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->payment_method));
    }
}
