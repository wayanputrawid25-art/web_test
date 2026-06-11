<?php

namespace App\Modules\StockOpname\Application\DTOs;

class UpdateStockOpnameSessionDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly ?string $countDeadline,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            countDeadline: $data['count_deadline'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'count_deadline' => $this->countDeadline,
        ], fn ($value) => $value !== null);
    }
}