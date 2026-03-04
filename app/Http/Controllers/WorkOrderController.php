<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Http\Requests\CompleteWorkOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WorkOrderController extends Controller
{
  public function index(Request $request)
    {
        $query = WorkOrder::with(['asset', 'technician', 'supervisor']);
        
        // technicians - they should only see their assigned work orders
        if (auth()->user()->hasRole('technician')) {
            $query->where('technician_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('technician_id')) {
            // Only admin/supervisor can filter by technician
            if (!auth()->user()->hasRole('technician')) {
                $query->where('technician_id', $request->technician_id);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $workOrders = $query->latest()->paginate(15)->withQueryString();
        
        $assets = Asset::all();
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        return view('work-orders.index', compact('workOrders', 'assets', 'technicians'));
    }

    public function create()
    {
        Gate::authorize('create', WorkOrder::class);

        $assets = Asset::where('status', 'operational')->get();
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        return view('work-orders.create', compact('assets', 'technicians'));
    }

    public function store(StoreWorkOrderRequest $request)
    {
        Gate::authorize('create', WorkOrder::class);

        $workOrder = WorkOrder::create($request->validated() + [
            'supervisor_id' => auth()->id(),
            'status' => 'pending'
        ]);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order created successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['asset', 'technician', 'supervisor', 'maintenanceSchedule', 'maintenanceLog']);
        
        return view('work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $assets = Asset::all();
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        return view('work-orders.edit', compact('workOrder', 'assets', 'technicians'));
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $workOrder->update($request->validated());

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order updated successfully.');
    }

    public function start(WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order started.');
    }

    public function complete(CompleteWorkOrderRequest $request, WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $workOrder->update([
            'status' => 'completed',
            'completed_date' => now(),
            'time_spent_minutes' => $request->time_spent_minutes,
            'checklist_responses' => $request->checklist_responses,
            'parts_used' => $request->parts_used,
            'technician_remarks' => $request->technician_remarks
        ]);

        // Create maintenance log
        $workOrder->maintenanceLog()->create([
            'asset_id' => $workOrder->asset_id,
            'performed_by' => auth()->id(),
            'maintenance_type' => $workOrder->type,
            'actions_taken' => $request->actions_taken,
            'measurements' => $request->measurements,
            'parts_used' => $request->parts_used,
            'time_spent_minutes' => $request->time_spent_minutes,
            'observations' => $request->technician_remarks,
            'result' => 'successful'
        ]);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order completed.');
    }

    public function verify(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('verify', $workOrder);

        $request->validate([
            'supervisor_remarks' => 'nullable|string'
        ]);

        $workOrder->update([
            'status' => 'verified',
            'verified_at' => now(),
            'supervisor_remarks' => $request->supervisor_remarks
        ]);

        // Update asset status if needed
        $workOrder->asset->update(['status' => 'operational']);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order verified.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        Gate::authorize('delete', $workOrder);

        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work order deleted successfully.');
    }
}