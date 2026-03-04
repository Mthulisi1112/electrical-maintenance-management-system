<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = auth()->user();
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // ---- ASSETS (Everyone can see all assets) ----
        $assets = Asset::where('name', 'like', "%{$query}%")
            ->orWhere('asset_code', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->orWhere('manufacturer', 'like', "%{$query}%")
            ->orWhere('model', 'like', "%{$query}%")
            ->orWhere('serial_number', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'type' => 'asset',
                    'type_label' => 'ASSET',
                    'title' => $asset->name,
                    'subtitle' => $asset->asset_code . ' • ' . $asset->location,
                    'url' => route('assets.show', $asset)
                ];
            });

        // ---- WORK ORDERS (Role-based filtering) ----
        $workOrdersQuery = WorkOrder::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('work_order_number', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Apply role-based filters
        if ($user->hasRole('technician')) {
            // Technicians only see their assigned work orders
            $workOrdersQuery->where('technician_id', $user->id);
        }
        // Admin and Supervisor see all work orders
        // Auditor sees all work orders (read-only)

        $workOrders = $workOrdersQuery->limit(5)
            ->get()
            ->map(function($wo) {
                return [
                    'id' => $wo->id,
                    'type' => 'work-order',
                    'type_label' => 'WORK ORDER',
                    'title' => $wo->title,
                    'subtitle' => $wo->work_order_number . ' • ' . ucfirst($wo->status) . ' • Technician: ' . ($wo->technician->name ?? 'Unassigned'),
                    'url' => route('work-orders.show', $wo)
                ];
            });

        // ---- FAULTS (Role-based filtering) ----
        $faultsQuery = Fault::where(function($q) use ($query) {
            $q->where('description', 'like', "%{$query}%")
              ->orWhere('fault_number', 'like', "%{$query}%")
              ->orWhere('root_cause', 'like', "%{$query}%");
        });

        // Apply role-based filters
        if ($user->hasRole('technician')) {
            // Technicians only see faults they reported or are assigned to
            $faultsQuery->where(function($q) use ($user) {
                $q->where('reported_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        $faults = $faultsQuery->limit(5)
            ->get()
            ->map(function($fault) {
                return [
                    'id' => $fault->id,
                    'type' => 'fault',
                    'type_label' => 'FAULT',
                    'title' => 'Fault #' . $fault->fault_number,
                    'subtitle' => $fault->description . ' • Severity: ' . ucfirst($fault->severity),
                    'url' => route('faults.show', $fault)
                ];
            });

        // ---- MAINTENANCE SCHEDULES (Everyone can see all schedules) ----
        $schedules = MaintenanceSchedule::with('asset')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'type' => 'schedule',
                    'type_label' => 'SCHEDULE',
                    'title' => $schedule->title,
                    'subtitle' => ucfirst($schedule->frequency) . ' • ' . ($schedule->asset->name ?? 'No Asset'),
                    'url' => route('maintenance-schedules.show', $schedule)
                ];
            });

        // ---- USERS (Admin only) ----
        if ($user->hasRole('admin')) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('employee_id', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'type' => 'user',
                        'type_label' => 'USER',
                        'title' => $user->name,
                        'subtitle' => $user->email . ' • ' . ($user->role->name ?? 'No Role'),
                        'url' => route('admin.users.edit', $user)
                    ];
                });
            
            $results = array_merge(
                $assets->toArray(),
                $workOrders->toArray(),
                $faults->toArray(),
                $schedules->toArray(),
                $users->toArray()
            );
        } else {
            $results = array_merge(
                $assets->toArray(),
                $workOrders->toArray(),
                $faults->toArray(),
                $schedules->toArray()
            );
        }

        // Sort by relevance (optional)
        // You could add scoring based on exact matches

        return response()->json($results);
    }
}