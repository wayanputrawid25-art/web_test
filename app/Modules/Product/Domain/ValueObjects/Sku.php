<?php

namespace App\Modules\Product\Domain\ValueObjects;

use App\Modules\Product\Exceptions\InvalidSkuException;

final class Sku
{
    private const MAX_LENGTH = 50;
    private const PATTERN = '/^[A-Z0-9\-]+$/';

    public function __construct(
        public readonly string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidSkuException('SKU tidak boleh kosong');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidSkuException('SKU tidak boleh lebih dari ' . self::MAX_LENGTH . ' karakter');
        }

        if (!preg_match(self::PATTERN, $value)) {
            throw new InvalidSkuException('SKU hanya boleh mengandung huruf kapital, angka, dan tanda hubung');
        }
    }

    public function equals(Sku $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}