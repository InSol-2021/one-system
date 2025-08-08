<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class AuditLogsComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $eventTypeFilter = '';
    public $dateFilter = '';
    public $showDetails = [];
    public $processing = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEventTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function toggleDetails($logId)
    {
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        try {
            if (isset($this->showDetails[$logId])) {
                unset($this->showDetails[$logId]);
            } else {
                $this->showDetails[$logId] = true;
            }

            $this->dispatch('$refresh');

        } finally {
            $this->processing = false;
        }
    }

    public function render()
    {
        $query = AuditLog::with(['user', 'clientSystem'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('action', 'ILIKE', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'ILIKE', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('username', 'ILIKE', '%' . $this->search . '%');
                  })
                  ->orWhereHas('clientSystem', function($clientQuery) {
                      $clientQuery->where('name', 'ILIKE', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->eventTypeFilter)) {
            $query->where('event_type', $this->eventTypeFilter);
        }

        if (!empty($this->dateFilter)) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        $auditLogs = $query->paginate(20);

        $eventTypes = AuditLog::distinct()
            ->pluck('event_type')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.admin.audit-logs-component', [
            'auditLogs' => $auditLogs,
            'eventTypes' => $eventTypes
        ]);
    }
}
