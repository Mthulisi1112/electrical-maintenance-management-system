<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use App\Http\Requests\StoreMaintenanceScheduleRequest;
use App\Http\Requests\UpdateMaintenanceScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with(['asset', 'creator']);

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'yes');
        }

        $schedules = $query->latest()->paginate(15)->withQueryString();
        
        $assets = Asset::all();

        return view('maintenance-schedules.index', compact('schedules', 'assets'));
    }

    public function create()
    {
        Gate::authorize('create', MaintenanceSchedule::class);

        $assets = Asset::where('status', 'operational')->get();

        return view('maintenance-schedules.create', compact('assets'));
    }

    public function store(StoreMaintenanceScheduleRequest $request)
    {
        Gate::authorize('create', MaintenanceSchedule::class);

        $schedule = MaintenanceSchedule::create($request->validated() + [
            'created_by' => auth()->id(),
            'next_due_date' => $request->start_date
        ]);

        return redirect()->route('maintenance-schedules.show', $schedule)
            ->with('success', 'Maintenance schedule created successfully.');
    }

    public function show(MaintenanceSchedule $maintenanceSchedule)
    {
        $maintenanceSchedule->load(['asset', 'creator', 'workOrders' => function($q) {
            $q->latest()->limit(10);
        }]);
        
        return view('maintenance-schedules.show', compact('maintenanceSchedule'));
    }

    public function edit(MaintenanceSchedule $maintenanceSchedule)
    {
        Gate::authorize('update', $maintenanceSchedule);

        $assets = Asset::all();

        return view('maintenance-schedules.edit', compact('maintenanceSchedule', 'assets'));
    }

    public function update(UpdateMaintenanceScheduleRequest $request, MaintenanceSchedule $maintenanceSchedule)
    {
        Gate::authorize('update', $maintenanceSchedule);

        $maintenanceSchedule->update($request->validated());

        return redirect()->route('maintenance-schedules.show', $maintenanceSchedule)
            ->with('success', 'Maintenance schedule updated successfully.');
    }

    public function destroy(MaintenanceSchedule $maintenanceSchedule)
    {
        Gate::authorize('delete', $maintenanceSchedule);

        $maintenanceSchedule->delete();

        return redirect()->route('maintenance-schedules.index')
            ->with('success', 'Maintenance schedule deleted successfully.');
    }

    public function toggleActive(MaintenanceSchedule $maintenanceSchedule)
    {
        Gate::authorize('update', $maintenanceSchedule);

        $maintenanceSchedule->update([
            'is_active' => !$maintenanceSchedule->is_active
        ]);

        return redirect()->route('maintenance-schedules.show', $maintenanceSchedule)
            ->with('success', 'Schedule ' . ($maintenanceSchedule->is_active ? 'activated' : 'deactivated') . ' successfully.');
    }

    public function generateWorkOrders(MaintenanceSchedule $maintenanceSchedule)
    {
        Gate::authorize('create', WorkOrder::class);

        // Get the first available technician
        $technician = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->first();

        if (!$technician) {
            return redirect()->back()
                ->with('error', 'No technician available. Please add a technician first.');
        }

        try {
            $workOrder = WorkOrder::create([
                'asset_id' => $maintenanceSchedule->asset_id,
                'maintenance_schedule_id' => $maintenanceSchedule->id,
                'technician_id' => $technician->id,
                'type' => 'preventive',
                'status' => 'pending',
                'title' => 'Scheduled Maintenance: ' . $maintenanceSchedule->title,
                'description' => $maintenanceSchedule->description,
                'checklist' => $maintenanceSchedule->checklist_items,
                'scheduled_date' => $maintenanceSchedule->next_due_date,
                'supervisor_id' => auth()->id()
            ]);

            // Debug: Check if work order was created
            \Log::info('Work Order Created:', [
                'id' => $workOrder->id,
                'work_order_number' => $workOrder->work_order_number,
                'title' => $workOrder->title
            ]);

            return redirect()->route('work-orders.show', $workOrder)
                ->with('success', 'Work order generated successfully and assigned to ' . $technician->name);

        } catch (\Exception $e) {
            \Log::error('Failed to create work order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create work order: ' . $e->getMessage());
        }
    }
}