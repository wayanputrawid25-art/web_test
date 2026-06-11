<?php

namespace App\Modules\Approval\Presentation\Livewire;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use App\Modules\Users\Infrastructure\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ApprovalCreate extends Component
{
    public string $type = '';
    public int $referenceId = 0;
    public string $title = '';
    public string $description = '';
    public ?int $approverId = null;
    public string $notes = '';

    public array $availableReferences = [];
    public ?string $referenceLabel = null;

    protected function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:stock_opname,stock_adjustment,inventory_correction,manual_adjustment'],
            'referenceId' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'approverId' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'type.required' => 'Approval type is required',
            'referenceId.required' => 'Please select a reference',
            'title.required' => 'Title is required',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-approvals');
    }

    public function render()
    {
        $approvers = User::role(['SuperAdmin', 'WarehouseAdmin'])
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]);

        return view('approval::create', [
            'types' => ApprovalType::cases(),
            'approvers' => $approvers,
            'availableReferences' => $this->availableReferences,
            'referenceLabel' => $this->referenceLabel,
        ]);
    }

    public function updatedType(string $value): void
    {
        $this->referenceId = 0;
        $this->availableReferences = [];
        $this->referenceLabel = null;

        if (!$value) {
            return;
        }

        $type = ApprovalType::from($value);

        $this->availableReferences = match ($type) {
            ApprovalType::STOCK_OPNAME => $this->getStockOpnameReferences(),
            ApprovalType::STOCK_ADJUSTMENT => $this->getStockAdjustmentReferences(),
            ApprovalType::INVENTORY_CORRECTION => $this->getInventoryCorrectionReferences(),
            ApprovalType::MANUAL_ADJUSTMENT => $this->getManualAdjustmentReferences(),
        };

        $this->referenceLabel = match ($type) {
            ApprovalType::STOCK_OPNAME => 'Select Stock Opname Session',
            ApprovalType::STOCK_ADJUSTMENT => 'Select Stock Adjustment',
            ApprovalType::INVENTORY_CORRECTION => 'Select Inventory Transaction',
            ApprovalType::MANUAL_ADJUSTMENT => 'Select Manual Adjustment',
        };
    }

    public function save(): void
    {
        $this->validate();

        try {
            $request = app(ApprovalService::class)->createRequest([
                'type' => $this->type,
                'reference_id' => $this->referenceId,
                'title' => $this->title,
                'description' => $this->description,
                'approver_id' => $this->approverId,
                'notes' => $this->notes,
            ]);

            session()->flash('success', 'Approval request created');

            $this->redirectRoute('approvals.show', $request->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirectRoute('approvals.index');
    }

    private function getStockOpnameReferences(): array
    {
        return \App\Modules\StockOpname\Infrastructure\Models\StockOpnameSession::pending()
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'label' => "{$s->code} - {$s->name}",
                'status' => $s->status->value,
            ])
            ->toArray();
    }

    private function getStockAdjustmentReferences(): array
    {
        // For future implementation
        return [];
    }

    private function getInventoryCorrectionReferences(): array
    {
        // For future implementation
        return [];
    }

    private function getManualAdjustmentReferences(): array
    {
        // For future implementation
        return [];
    }
}