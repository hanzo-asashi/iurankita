<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OccupancyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Household extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'house_code',
        'block',
        'house_number',
        'head_of_family',
        'kk_number',
        'phone',
        'address',
        'occupancy_status',
        'ownership_status',
        'monthly_fee_override',
        'owner_name',
        'owner_phone',
        'notes',
        'is_active',
    ];

    /**
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany<ConstructionProject, $this>
     */
    public function constructionProjects(): HasMany
    {
        return $this->hasMany(ConstructionProject::class);
    }

    /**
     * @return HasManyThrough<Payment, Invoice, $this>
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }

    /**
     * @param  Builder<Household>  $query
     * @return Builder<Household>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Household>  $query
     * @return Builder<Household>
     */
    public function scopeOccupied(Builder $query): Builder
    {
        return $query->where('occupancy_status', OccupancyStatus::Occupied);
    }

    /**
     * @param  Builder<Household>  $query
     * @return Builder<Household>
     */
    public function scopeUnoccupied(Builder $query): Builder
    {
        return $query->where('occupancy_status', OccupancyStatus::Unoccupied);
    }

    public function getFullLabelAttribute(): string
    {
        return "{$this->house_code} - {$this->head_of_family} ({$this->block}/{$this->house_number})";
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occupancy_status' => OccupancyStatus::class,
            'monthly_fee_override' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
