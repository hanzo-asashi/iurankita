<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class BillingResult
{
    public function __construct(
        public int $amount,
        public string $feeCode,
        public string $feeName,
        public string $description,
    ) {}
}
