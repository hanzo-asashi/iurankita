<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentConfirmationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PaymentConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'invoice_id',
        'sender_name',
        'sender_bank',
        'amount',
        'payment_date',
        'proof_path',
        'notes',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === PaymentConfirmationStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === PaymentConfirmationStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === PaymentConfirmationStatus::Rejected;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PaymentConfirmationStatus::class,
            'payment_date' => 'date',
            'amount' => 'integer',
            'verified_at' => 'datetime',
        ];
    }
}
