<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'household_id',
        'invoice_type',
        'billing_period',
        'issue_date',
        'due_date',
        'subtotal',
        'total_amount',
        'amount_paid',
        'balance',
        'status',
        'construction_project_id',
        'notes',
    ];

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * @return BelongsTo<ConstructionProject, $this>
     */
    public function constructionProject(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class);
    }

    /**
     * @return HasMany<InvoiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeMonthly(Builder $query): Builder
    {
        return $query->where('invoice_type', InvoiceType::Monthly);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeConstruction(Builder $query): Builder
    {
        return $query->where('invoice_type', InvoiceType::Construction);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeSpecial(Builder $query): Builder
    {
        return $query->where('invoice_type', InvoiceType::Special);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue]);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    public function isMonthly(): bool
    {
        return $this->invoice_type === InvoiceType::Monthly;
    }

    public function isConstruction(): bool
    {
        return $this->invoice_type === InvoiceType::Construction;
    }

    public function isSpecial(): bool
    {
        return $this->invoice_type === InvoiceType::Special;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_type' => InvoiceType::class,
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'integer',
            'total_amount' => 'integer',
            'amount_paid' => 'integer',
            'balance' => 'integer',
        ];
    }
}
