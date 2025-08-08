<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('audit_logs as al')
                ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
                ->select(
                    'al.*',
                    'u.username'
                );

            if ($request->filled('event_type')) {
                $query->where('al.event_type', $request->event_type);
            }

            if ($request->filled('user_id')) {
                $query->where('al.user_id', $request->user_id);
            }

            if ($request->filled('date_from')) {
                $query->where('al.created_at', '>=', $request->date_from . ' 00:00:00');
            }

            if ($request->filled('date_to')) {
                $query->where('al.created_at', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('al.description', 'LIKE', "%$search%")
                      ->orWhere('al.ip_address', 'LIKE', "%$search%")
                      ->orWhere('u.username', 'LIKE', "%$search%");
                });
            }

            $perPage = $request->per_page ?? 50;
            $page = $request->page ?? 1;
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $logs = $query->orderBy('al.created_at', 'desc')
                         ->offset($offset)
                         ->limit($perPage)
                         ->get();

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => ceil($total / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $log = DB::table('audit_logs as al')
                ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
                ->select(
                    'al.*',
                    'u.username',
                    'u.email'
                )
                ->where('al.id', $id)
                ->first();

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit log not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'log' => $log
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = DB::table('audit_logs as al')
                ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
                ->select(
                    'al.id',
                    'al.event_type',
                    'al.description',
                    'al.ip_address',
                    'al.user_agent',
                    'al.created_at',
                    'u.username'
                );

            if ($request->filled('event_type')) {
                $query->where('al.event_type', $request->event_type);
            }

            if ($request->filled('user_id')) {
                $query->where('al.user_id', $request->user_id);
            }

            if ($request->filled('date_from')) {
                $query->where('al.created_at', '>=', $request->date_from . ' 00:00:00');
            }

            if ($request->filled('date_to')) {
                $query->where('al.created_at', '<=', $request->date_to . ' 23:59:59');
            }

            $logs = $query->orderBy('al.created_at', 'desc')->get();

            $csvContent = "ID,Event Type,Description,Username,IP Address,User Agent,Created At\n";

            foreach ($logs as $log) {
                $csvContent .= sprintf(
                    "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                    $log->id,
                    $log->event_type,
                    str_replace('"', '""', $log->description),
                    $log->username ?? 'N/A',
                    $log->ip_address,
                    str_replace('"', '""', $log->user_agent),
                    $log->created_at
                );
            }

            $this->logAuditEvent('audit_logs_exported', 'Audit logs exported', [
                'exported_count' => count($logs),
                'filters' => $request->all()
            ]);

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="audit_logs_' . date('Y-m-d_H-i-s') . '.csv"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export audit logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        try {
            $stats = [
                'total_events' => DB::table('audit_logs')->count(),
                'today_events' => DB::table('audit_logs')
                    ->whereDate('created_at', today())
                    ->count(),
                'week_events' => DB::table('audit_logs')
                    ->where('created_at', '>=', now()->subWeek())
                    ->count(),
                'top_events' => DB::table('audit_logs')
                    ->select('event_type', DB::raw('COUNT(*) as count'))
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('event_type')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'top_users' => DB::table('audit_logs as al')
                    ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
                    ->select('u.username', DB::raw('COUNT(*) as count'))
                    ->where('al.created_at', '>=', now()->subDays(30))
                    ->whereNotNull('al.user_id')
                    ->groupBy('u.username')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'recent_activity' => DB::table('audit_logs as al')
                    ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
                    ->select('al.event_type', 'al.description', 'al.created_at', 'u.username')
                    ->orderBy('al.created_at', 'desc')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cleanup(Request $request)
    {
        try {
            $daysToKeep = $request->days_to_keep ?? 90;

            $deletedCount = DB::table('audit_logs')
                ->where('created_at', '<', now()->subDays($daysToKeep))
                ->delete();

            $this->logAuditEvent('audit_logs_cleanup', "Audit logs cleanup performed, deleted logs older than $daysToKeep days", [
                'days_to_keep' => $daysToKeep,
                'deleted_count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Deleted $deletedCount old audit logs"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup audit logs: ' . $e->getMessage()
            ], 500);
        }
    }

    private function logAuditEvent($event, $description, $data = [])
    {
        try {
            DB::table('audit_logs')->insert([
                'event_type' => $event,
                'description' => $description,
                'user_id' => session('user_id'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'data' => json_encode($data),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            // Silently fail audit logging to not disrupt main functionality
        }
    }
}
