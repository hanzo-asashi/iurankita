<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'complex_name',
        'address',
        'phone',
        'email',
        'logo',
        'favicon',
        'tagline',
        'footer_text',
        'primary_color',
        'monthly_due_day',
    ];

    public static function current(): self
    {
        /** @var self $setting */
        $setting = self::query()->firstOrCreate(
            ['id' => 1],
            [
                'app_name' => 'IuranKita',
                'complex_name' => 'Del Mattappa Residence',
                'address' => 'Jl. Kemakmuran No.45 Blok A5, Kab. Soppeng',
                'phone' => '0812-3456-7890',
                'email' => 'pengelola@iurankita.test',
                'tagline' => 'Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.',
                'footer_text' => 'Sistem Administrasi dan Pembayaran Iuran Warga Mandiri & Terpercaya.',
                'primary_color' => '#0F766E',
                'monthly_due_day' => 10,
            ]
        );

        return $setting;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_due_day' => 'integer',
        ];
    }
}
