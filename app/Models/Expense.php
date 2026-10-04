<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'category',
        'title',
        'amount',
        'expense_date',
        'recipient',
        'receipt_path',
        'notes',
        'created_by',
    ];

    public static function generateExpenseNumber(?Carbon $date = null): string
    {
        $d = $date ?? Carbon::now();
        $prefix = 'EXP/'.$d->format('Ym');
        $count = self::query()->where('expense_number', 'like', "{$prefix}/%")->count() + 1;

        return sprintf('%s/%04d', $prefix, $count);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'integer',
            'expense_date' => 'date',
        ];
    }
}
