<?php

namespace App\Modules\Approval\Presentation\Livewire;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Application\DTOs\ApprovalDecisionDTO;
use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\Entities\ApprovalDecision;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ApprovalView extends Component
{
    public ?ApprovalRequest $request = null;
    public array $activityLogs = [];
    public ?ApprovalDecision $decision = null;
    public string $newComment = '';
    public string $decisionComments = '';
    public string $showDecisionModal = '';

    protected $rules = [
        'newComment' => ['nullable', 'string', 'max:1000'],
        'decisionComments' => ['nullable', 'string', 'max:1000'],
    ];

    public function mount(int $id): void
    {
        Gate::authorize('view-approvals');

        $this->loadRequest($id);
    }

    public function render()
    {
        return view('approval::view');
    }

    public function approve(): void
    {
        Gate::authorize('edit-approvals');

        $this->validate(['decisionComments' => 'required|string|max:1000']);

        try {
            app(ApprovalService::class)->approve($this->request->id, [
                'comments' => $this->decisionComments,
            ]);

            $this->loadRequest($this->request->id);
            $this->decisionComments = '';
            $this->showDecisionModal = '';
            session()->flash('success', 'Request approved');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject(): void
    {
        Gate::authorize('edit-approvals');

        $this->validate(['decisionComments' => 'required|string|max:1000']);

        try {
            app(ApprovalService::class)->reject($this->request->id, [
                'comments' => $this->decisionComments,
            ]);

            $this->loadRequest($this->request->id);
            $this->decisionComments = '';
            $this->showDecisionModal = '';
            session()->flash('success', 'Request rejected');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function requestRevision(): void
    {
        Gate::authorize('edit-approvals');

        $this->validate(['decisionComments' => 'required|string|max:1000']);

        try {
            app(ApprovalService::class)->requestRevision($this->request->id, [
                'comments' => $this->decisionComments,
            ]);

            $this->loadRequest($this->request->id);
            $this->decisionComments = '';
            $this->showDecisionModal = '';
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

        Gate::authorize('edit-approvals');

        try {
            app(ApprovalService::class)->addComment($this->request->id, $this->newComment);

            $this->loadRequest($this->request->id);
            $this->newComment = '';
            session()->flash('success', 'Comment added');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openDecisionModal(string $action): void
    {
        $this->showDecisionModal = $action;
        $this->decisionComments = '';
    }

    public function closeDecisionModal(): void
    {
        $this->showDecisionModal = '';
        $this->decisionComments = '';
    }

    private function loadRequest(int $id): void
    {
        $data = app(ApprovalService::class)->getRequestWithLogs($id);
        $this->request = $data['request'];
        $this->activityLogs = $data['activityLogs']->toArray();
        $this->decision = $data['decision'];
    }

    public function canApprove(): bool
    {
        if (!$this->request) {
            return false;
        }

        // Cannot approve own request
        if ($this->request->requesterId === auth()->id()) {
            return false;
        }

        return $this->request->isPending();
    }

    public function back(): void
    {
        $this->redirectRoute('approvals.index');
    }
}