<?php

namespace App\Modules\StockOpname\Presentation\Livewire;

use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StockOpnameView extends Component
{
    public ?StockOpnameSession $session = null;
    public array $items = [];
    public array $activityLogs = [];
    public array $assignments = [];
    public string $newComment = '';

    protected $rules = [
        'newComment' => ['nullable', 'string', 'max:1000'],
    ];

    public function mount(int $id): void
    {
        Gate::authorize('view-stock-opnames');

        $this->loadSession($id);
    }

    public function render()
    {
        return view('stock_opname::view');
    }

    public function changeStatus(string $newStatus, ?string $notes = null): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            app(StockOpnameService::class)->changeStatus($this->session->id, [
                'status' => $newStatus,
                'notes' => $notes,
            ]);

            $this->loadSession($this->session->id);
            session()->flash('success', 'Status berhasil diubah');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function submitForReview(): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            app(StockOpnameService::class)->submitForReview($this->session->id);

            $this->loadSession($this->session->id);
            session()->flash('success', 'Submitted for review');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approve(): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            app(StockOpnameService::class)->approve($this->session->id);

            $this->loadSession($this->session->id);
            session()->flash('success', 'Stock Opname approved');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function requestRevision(string $reason): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            app(StockOpnameService::class)->requestRevision($this->session->id, $reason);

            $this->loadSession($this->session->id);
            session()->flash('success', 'Revision requested');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function addComment(): void
    {
        if (empty($this->newComment)) {
            return;
        }

        Gate::authorize('edit-stock-opnames');

        app(StockOpnameService::class)->addComment($this->session->id, $this->newComment);

        $this->loadSession($this->session->id);
        $this->newComment = '';
        session()->flash('success', 'Comment added');
    }

    private function loadSession(int $id): void
    {
        $data = app(StockOpnameService::class)->getSessionWithLogs($id);
        $this->session = $data['session'];
        $this->items = $data['items']->toArray();
        $this->activityLogs = $data['activityLogs']->toArray();
        $this->assignments = $data['assignments']->toArray();
    }

    public function getNextStatuses(): array
    {
        return $this->session?->getNextStatuses() ?? [];
    }

    public function getVarianceSummary(): array
    {
        $positive = collect($this->items)->filter(fn ($i) => $i['variance'] > 0)->count();
        $negative = collect($this->items)->filter(fn ($i) => $i['variance'] < 0)->count();
        $matched = collect($this->items)->filter(fn ($i) => $i['variance'] == 0)->count();

        return [
            'positive' => $positive,
            'negative' => $negative,
            'matched' => $matched,
            'total' => count($this->items),
        ];
    }

    public function back(): void
    {
        $this->redirectRoute('stock_opnames.index');
    }
}