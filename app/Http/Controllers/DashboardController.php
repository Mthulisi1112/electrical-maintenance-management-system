<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        // If user is not logged in, show public dashboard
        if (!auth()->check()) {
            return view('dashboard-public');
        }
        
        $user = auth()->user();
        
        // Base stats that everyone can see (filtered by policies)
        $stats = [];
        
        if (Gate::allows('viewAny', Asset::class)) {
            $stats['total_assets'] = Asset::count();
        }
        
        if (Gate::allows('viewAny', WorkOrder::class)) {
            // For technicians, only count their own work orders
            if ($user->can('viewAssignedOnly', WorkOrder::class)) {
                $stats['active_work_orders'] = WorkOrder::where('technician_id', $user->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count();
            } else {
                $stats['active_work_orders'] = WorkOrder::whereIn('status', ['pending', 'in_progress'])->count();
            }
        }
        
        if (Gate::allows('viewAny', Fault::class)) {
            // For technicians, only count critical faults they're involved with
            if ($user->hasRole('technician')) {
                $stats['critical_faults'] = Fault::where('severity', 'critical')
                    ->where('status', '!=', 'resolved')
                    ->where(function($q) use ($user) {
                        $q->where('reported_by', $user->id)
                          ->orWhere('assigned_to', $user->id);
                    })
                    ->count();
            } else {
                $stats['critical_faults'] = Fault::where('severity', 'critical')
                    ->where('status', '!=', 'resolved')
                    ->count();
            }
        }
        
        if (Gate::allows('viewAny', MaintenanceSchedule::class)) {
            $stats['overdue_maintenance'] = MaintenanceSchedule::where('is_active', true)
                ->where('next_due_date', '<', now())
                ->count();
        }

        // Recent Work Orders (filtered by role)
        $workOrdersQuery = WorkOrder::with(['asset', 'technician']);
        
        if ($user->hasRole('technician')) {
            $workOrdersQuery->where('technician_id', $user->id);
        } elseif ($user->hasRole('auditor')) {
            $workOrdersQuery->whereIn('status', ['completed', 'verified']);
        }
        
        $recentWorkOrders = $workOrdersQuery->latest()->take(5)->get();

        // Recent Faults (filtered by role)
        $faultsQuery = Fault::with(['asset', 'reportedBy']);
        
        if ($user->hasRole('technician')) {
            $faultsQuery->where(function($q) use ($user) {
                $q->where('reported_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }
        
        $recentFaults = $faultsQuery->latest()->take(5)->get();

        // Charts - Only for admin, supervisor, and auditor (not technicians)
        $assetsByStatus = [];
        $workOrdersByStatus = [];
        
        if (!$user->hasRole('technician') && Gate::allows('viewAny', Asset::class)) {
            $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
        }
        
        if (!$user->hasRole('technician') && Gate::allows('viewAny', WorkOrder::class)) {
            $workOrdersByStatus = WorkOrder::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
        }

        // Additional data for supervisors
        $pendingVerification = null;
        if ($user->can('verifyAny', WorkOrder::class)) {
            $pendingVerification = WorkOrder::where('status', 'completed')->count();
        }

        // Additional data for admins
        $recentAuditLogs = null;
        if ($user->hasRole('admin')) {
            // You'll need to create an AuditLog model and migration
            // $recentAuditLogs = \App\Models\AuditLog::latest()->take(5)->get();
        }

        // Upcoming maintenance (everyone can see this)
        $upcomingMaintenance = MaintenanceSchedule::with('asset')
            ->where('is_active', true)
            ->where('next_due_date', '>=', now())
            ->where('next_due_date', '<=', now()->addDays(7))
            ->orderBy('next_due_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentWorkOrders',
            'recentFaults',
            'assetsByStatus',
            'workOrdersByStatus',
            'upcomingMaintenance',
            'pendingVerification',
            'recentAuditLogs'
        ));
    }
}