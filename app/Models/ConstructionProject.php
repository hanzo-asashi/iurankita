<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ConstructionProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'household_id',
        'project_type',
        'description',
        'start_date',
        'completion_date',
        'status',
        'one_time_fee',
        'fee_invoice_id',
        'created_by',
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
     * @return HasOne<Invoice, $this>
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'construction_project_id');
    }

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function feeInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'fee_invoice_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param  Builder<ConstructionProject>  $query
     * @return Builder<ConstructionProject>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ConstructionStatus::Active);
    }

    /**
     * @param  Builder<ConstructionProject>  $query
     * @return Builder<ConstructionProject>
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ConstructionStatus::Completed);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_type' => ConstructionType::class,
            'status' => ConstructionStatus::class,
            'start_date' => 'date',
            'completion_date' => 'date',
            'one_time_fee' => 'integer',
        ];
    }
}
