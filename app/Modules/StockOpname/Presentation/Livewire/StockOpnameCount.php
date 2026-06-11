<?php

namespace App\Modules\StockOpname\Presentation\Livewire;

use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockOpnameCount extends Component
{
    public ?StockOpnameSession $session = null;
    public array $items = [];
    public array $countValues = [];
    public array $notesValues = [];

    protected $rules = [
        'countValues.*' => ['nullable', 'numeric', 'min:0'],
        'notesValues.*' => ['nullable', 'string', 'max:500'],
    ];

    public function mount(int $id): void
    {
        Gate::authorize('edit-stock-opnames');

        $this->loadSession($id);
    }

    public function render()
    {
        return view('stock_opname::count');
    }

    public function saveDraft(): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            foreach ($this->countValues as $itemId => $count) {
                if ($count === '' || $count === null) {
                    continue;
                }

                app(StockOpnameService::class)->countItem((int) $itemId, [
                    'counted_quantity' => (float) $count,
                    'notes' => $this->notesValues[$itemId] ?? null,
                ]);
            }

            $this->loadSession($this->session->id);
            session()->flash('success', 'Draft saved successfully');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function saveAndSubmit(): void
    {
        Gate::authorize('edit-stock-opnames');

        // First save all counts
        $this->saveDraft();

        // Then submit for review
        try {
            app(StockOpnameService::class)->submitForReview($this->session->id);

            session()->flash('success', 'Saved and submitted for review');
            $this->redirectRoute('stock_opnames.show', $this->session->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function loadSession(int $id): void
    {
        $this->session = app(StockOpnameService::class)->getSession($id);
        $this->items = app(StockOpnameService::class)->getItems($id)->toArray();

        // Initialize count values from existing data
        foreach ($this->items as $item) {
            if (!isset($this->countValues[$item['id']])) {
                $this->countValues[$item['id']] = $item['counted_quantity'] ?? '';
            }
            if (!isset($this->notesValues[$item['id']])) {
                $this->notesValues[$item['id']] = $item['notes'] ?? '';
            }
        }
    }

    public function getCountedProgress(): array
    {
        $counted = collect($this->items)->filter(fn ($i) => $i['counted_quantity'] !== null)->count();
        $total = count($this->items);
        $percentage = $total > 0 ? round(($counted / $total) * 100) : 0;

        return [
            'counted' => $counted,
            'total' => $total,
            'percentage' => $percentage,
        ];
    }

    public function back(): void
    {
        $this->redirectRoute('stock_opnames.show', $this->session->id);
    }
}