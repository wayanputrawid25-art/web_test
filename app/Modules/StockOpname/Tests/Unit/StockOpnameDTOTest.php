<?php

namespace App\Modules\StockOpname\Tests\Unit;

use App\Modules\StockOpname\Application\DTOs\CreateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\UpdateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\StockOpnameFilterDTO;
use App\Modules\StockOpname\Application\DTOs\CountItemDTO;
use App\Modules\StockOpname\Application\DTOs\ChangeStatusDTO;
use PHPUnit\Framework\TestCase;

class StockOpnameDTOTest extends TestCase
{
    public function test_create_session_dto_from_array(): void
    {
        $data = [
            'name' => 'Monthly Stock Opname',
            'description' => 'January 2024 stock opname',
            'task_id' => 5,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'count_deadline' => '2024-01-15',
            'product_ids' => [1, 2, 3],
        ];

        $dto = CreateStockOpnameSessionDTO::fromArray($data);

        $this->assertEquals('Monthly Stock Opname', $dto->name);
        $this->assertEquals('January 2024 stock opname', $dto->description);
        $this->assertEquals(5, $dto->taskId);
        $this->assertEquals('2024-01-01', $dto->startDate);
        $this->assertEquals([1, 2, 3], $dto->productIds);
    }

    public function test_create_session_dto_to_array(): void
    {
        $dto = new CreateStockOpnameSessionDTO(
            name: 'Test Session',
            description: 'Test description',
            taskId: null,
            startDate: '2024-01-01',
            endDate: '2024-01-31',
            countDeadline: '2024-01-15',
            productIds: [1, 2]
        );

        $array = $dto->toArray();

        $this->assertEquals('Test Session', $array['name']);
        $this->assertEquals('Test description', $array['description']);
        $this->assertEquals('created', $array['status']);
        $this->assertArrayHasKey('creator_id', $array);
        $this->assertArrayNotHasKey('product_ids', $array);
    }

    public function test_update_session_dto_from_array(): void
    {
        $data = [
            'name' => 'Updated Session',
            'description' => 'Updated description',
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-28',
            'count_deadline' => '2024-02-15',
        ];

        $dto = UpdateStockOpnameSessionDTO::fromArray($data);

        $this->assertEquals('Updated Session', $dto->name);
        $this->assertEquals('Updated description', $dto->description);
        $this->assertEquals('2024-02-01', $dto->startDate);
    }

    public function test_filter_dto_defaults(): void
    {
        $dto = StockOpnameFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->status);
        $this->assertFalse($dto->myAssignments);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_filter_dto_with_values(): void
    {
        $dto = StockOpnameFilterDTO::fromArray([
            'search' => 'monthly',
            'status' => 'counting',
            'creator_id' => 5,
            'my_assignments' => true,
            'per_page' => 25,
        ]);

        $this->assertEquals('monthly', $dto->search);
        $this->assertEquals('counting', $dto->status);
        $this->assertEquals(5, $dto->creatorId);
        $this->assertTrue($dto->myAssignments);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_count_item_dto(): void
    {
        $dto = CountItemDTO::fromArray([
            'counted_quantity' => 105.50,
            'notes' => 'Found extra items',
        ]);

        $this->assertEquals(105.50, $dto->countedQuantity);
        $this->assertEquals('Found extra items', $dto->notes);
    }

    public function test_change_status_dto(): void
    {
        $dto = ChangeStatusDTO::fromArray([
            'status' => 'approved',
            'notes' => 'All items verified',
        ]);

        $this->assertEquals('approved', $dto->newStatus);
        $this->assertEquals('All items verified', $dto->notes);
    }
}