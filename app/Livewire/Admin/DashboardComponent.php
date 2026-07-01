<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ClientSystem;
use App\Models\AuditLog;
use App\Models\IpWhitelist;
use App\Livewire\Concerns\AuthorizesAdmin;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    use AuthorizesAdmin;

    public $selectedPeriod = '7days';

    public function mount()
    {
        $this->authorizeAdmin();
    }

    public function render()
    {
        $stats = $this->getSystemStats();

        $activityData = $this->getActivityData();

        $recentActivity = $this->getRecentActivity();

        return view('livewire.admin.dashboard-component', [
            'stats' => $stats,
            'activityData' => $activityData,
            'recentActivity' => $recentActivity
        ]);
    }

    private function getSystemStats()
    {
        try {
            $totalUsers = User::count();
            $adminUsers = User::where('role', 'admin')->count();
            $regularUsers = User::where('role', 'user')->count();

            $totalClientSystems = ClientSystem::count();
            $activeClientSystems = ClientSystem::where('is_active', true)->count();

            $totalLogins24h = AuditLog::where('event_type', 'login')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $successfulLogins24h = AuditLog::where('event_type', 'login')
                ->where('success', true)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $failedLogins24h = $totalLogins24h - $successfulLogins24h;

            $ssoTokens24h = AuditLog::where('event_type', 'sso_token_generated')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $ipWhitelistEntries = IpWhitelist::count();

            return [
                'users' => [
                    'total' => $totalUsers,
                    'admin' => $adminUsers,
                    'regular' => $regularUsers,
                ],
                'client_systems' => [
                    'total' => $totalClientSystems,
                    'active' => $activeClientSystems,
                    'inactive' => $totalClientSystems - $activeClientSystems,
                ],
                'authentication' => [
                    'total_logins_24h' => $totalLogins24h,
                    'successful_logins_24h' => $successfulLogins24h,
                    'failed_logins_24h' => $failedLogins24h,
                    'success_rate' => $totalLogins24h > 0 ? round(($successfulLogins24h / $totalLogins24h) * 100, 1) : 0,
                ],
                'sso' => [
                    'tokens_24h' => $ssoTokens24h,
                ],
                'security' => [
                    'ip_whitelist_entries' => $ipWhitelistEntries,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'users' => ['total' => 0, 'admin' => 0, 'regular' => 0],
                'client_systems' => ['total' => 0, 'active' => 0, 'inactive' => 0],
                'authentication' => ['total_logins_24h' => 0, 'successful_logins_24h' => 0, 'failed_logins_24h' => 0, 'success_rate' => 0],
                'sso' => ['tokens_24h' => 0],
                'security' => ['ip_whitelist_entries' => 0]
            ];
        }
    }

    private function getActivityData()
    {
        try {
            $days = $this->selectedPeriod === '7days' ? 7 : ($this->selectedPeriod === '30days' ? 30 : 1);

            $activityData = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');

                $logins = AuditLog::where('event_type', 'login')
                    ->whereDate('created_at', $date)
                    ->count();

                $ssoTokens = AuditLog::where('event_type', 'sso_token_generated')
                    ->whereDate('created_at', $date)
                    ->count();

                $activityData[] = [
                    'date' => $dateStr,
                    'label' => $date->format('M d'),
                    'logins' => $logins,
                    'sso_tokens' => $ssoTokens,
                ];
            }

            return $activityData;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getRecentActivity()
    {
        try {
            return AuditLog::with(['user:id,username', 'clientSystem:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'event_type' => $log->event_type,
                        'action' => $log->action,
                        'description' => $log->description,
                        'success' => $log->success,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at,
                        'username' => $log->user?->username ?? 'System',
                        'client_system_name' => $log->clientSystem?->name ?? 'N/A'
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    public function updatedSelectedPeriod()
    {
        $this->render();
    }
}