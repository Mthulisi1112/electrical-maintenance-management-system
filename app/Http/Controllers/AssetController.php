<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('creator');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('asset_code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(15)->withQueryString();
        
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        Gate::authorize('create', Asset::class);
        
        return view('assets.create');
    }

    public function store(StoreAssetRequest $request)
    {
        Gate::authorize('create', Asset::class);

        $validated = $request->validated();
        $validated['created_by'] = auth()->id();
        
        $asset = Asset::create($validated);
        
        // Generate QR code after creation
        $asset->generateQrCode();

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'creator', 
            'maintenanceSchedules' => function($q) {
                $q->latest()->limit(5);
            }, 
            'workOrders' => function($q) {
                $q->with(['technician'])->latest()->limit(10);
            }, 
            'faults' => function($q) {
                $q->with(['reportedBy'])->latest()->limit(10);
            }
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        Gate::authorize('update', $asset);
        
        return view('assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        Gate::authorize('update', $asset);

        $validated = $request->validated();
        $asset->update($validated);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }
    public function destroy(Asset $asset)
    {
        Gate::authorize('delete', $asset);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function qrCode(Request $request, Asset $asset)
    {
        $qrCode = QrCode::size(300)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->margin(1)
            ->generate(route('assets.show', $asset));
        
        // If download parameter is present, return as download
        if ($request->has('download')) {
            return response($qrCode)
                ->header('Content-type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="qrcode-' . $asset->asset_code . '.svg"');
        }
        
        return response($qrCode)->header('Content-type', 'image/svg+xml');
    }
}