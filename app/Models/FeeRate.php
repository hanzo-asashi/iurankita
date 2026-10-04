<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class FeeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'amount',
        'effective_from',
        'effective_until',
        'is_active',
        'description',
    ];

    public static function getActiveRate(string $code, ?CarbonInterface $date = null): ?self
    {
        $targetDate = ($date ?? now())->format('Y-m-d');

        return self::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->where('effective_from', '<=', $targetDate)
            ->where(function (Builder $query) use ($targetDate): void {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $targetDate);
            })
            ->latest('effective_from')
            ->first();
    }

    /**
     * @param  Builder<FeeRate>  $query
     * @return Builder<FeeRate>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
