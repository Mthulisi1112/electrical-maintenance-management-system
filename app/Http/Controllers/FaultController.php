<?php

namespace App\Http\Controllers;

use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;
use App\Http\Requests\StoreFaultRequest;
use App\Http\Requests\UpdateFaultRequest;
use App\Http\Requests\ResolveFaultRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FaultController extends Controller
{
   public function index(Request $request)
    {
        $query = Fault::with(['asset', 'reportedBy', 'assignedTo']);

        // Filter for technicians - they should only see faults they reported or are assigned to
        if (auth()->user()->hasRole('technician')) {
            $query->where(function($q) {
                $q->where('reported_by', auth()->id())
                ->orWhere('assigned_to', auth()->id());
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('assigned_to')) {
            // Only admin/supervisor can filter by assigned technician
            if (!auth()->user()->hasRole('technician')) {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $faults = $query->latest()->paginate(15)->withQueryString();
        
        $assets = Asset::all();
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        return view('faults.index', compact('faults', 'assets', 'technicians'));
    }

    public function create()
    {
        Gate::authorize('create', Fault::class);

        $assets = Asset::whereIn('status', ['operational', 'faulty'])->get();

        return view('faults.create', compact('assets'));
    }

    public function store(StoreFaultRequest $request)
    {
        Gate::authorize('create', Fault::class);

        $fault = Fault::create($request->validated() + [
            'reported_by' => auth()->id(),
            'status' => 'reported',
            'downtime_start' => now()
        ]);

        // Update asset status
        $fault->asset->update(['status' => 'faulty']);

        return redirect()->route('faults.show', $fault)
            ->with('success', 'Fault reported successfully.');
    }

    public function show(Fault $fault)
    {
        $fault->load(['asset', 'reportedBy', 'assignedTo', 'workOrders']);
        
        return view('faults.show', compact('fault'));
    }

    public function edit(Fault $fault)
    {
        Gate::authorize('update', $fault);

        $assets = Asset::all();
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        return view('faults.edit', compact('fault', 'assets', 'technicians'));
    }

    public function update(UpdateFaultRequest $request, Fault $fault)
    {
        Gate::authorize('update', $fault);

        $fault->update($request->validated());

        return redirect()->route('faults.show', $fault)
            ->with('success', 'Fault updated successfully.');
    }

    public function assign(Request $request, Fault $fault)
    {
        Gate::authorize('assign', $fault);

        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $fault->assignTo(User::find($request->assigned_to));

        return redirect()->route('faults.show', $fault)
            ->with('success', 'Fault assigned successfully.');
    }

    public function resolve(ResolveFaultRequest $request, Fault $fault)
    {
        Gate::authorize('resolve', $fault);

        $fault->resolve(
            $request->root_cause,
            $request->corrective_actions,
            $request->parts_replaced
        );

        // Update asset status back to operational
        $fault->asset->update(['status' => 'operational']);

        return redirect()->route('faults.show', $fault)
            ->with('success', 'Fault resolved successfully.');
    }

    public function destroy(Fault $fault)
    {
        Gate::authorize('delete', $fault);

        $fault->delete();

        return redirect()->route('faults.index')
            ->with('success', 'Fault deleted successfully.');
    }
}