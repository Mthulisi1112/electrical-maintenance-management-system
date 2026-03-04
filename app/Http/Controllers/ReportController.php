<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function assets(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $assets = $query->withCount(['workOrders', 'faults', 'maintenanceLogs'])->get();

        $summary = [
            'total' => $assets->count(),
            'by_status' => $assets->groupBy('status')->map->count(),
            'by_type' => $assets->groupBy('type')->map->count(),
            'total_maintenance_hours' => MaintenanceLog::sum('time_spent_minutes') / 60,
            'total_faults' => Fault::count(),
            'average_downtime' => Fault::avg('downtime_minutes')
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.assets', compact('assets', 'summary'));
            return $pdf->download('assets-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return view('reports.assets', compact('assets', 'summary'));
    }

    public function maintenance(Request $request)
    {
        $query = MaintenanceLog::with(['asset', 'performedBy', 'workOrder']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        $logs = $query->latest()->get();

        $summary = [
            'total_logs' => $logs->count(),
            'total_hours' => $logs->sum('time_spent_minutes') / 60,
            'by_type' => $logs->groupBy('maintenance_type')->map->count(),
            'by_result' => $logs->groupBy('result')->map->count(),
            'average_time' => $logs->avg('time_spent_minutes')
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.maintenance', compact('logs', 'summary'));
            return $pdf->download('maintenance-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return view('reports.maintenance', compact('logs', 'summary'));
    }

    public function faults(Request $request)
    {
        $query = Fault::with(['asset', 'reportedBy', 'assignedTo']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fault_type')) {
            $query->where('fault_type', $request->fault_type);
        }

        $faults = $query->get();

        $summary = [
            'total_faults' => $faults->count(),
            'by_severity' => $faults->groupBy('severity')->map->count(),
            'by_type' => $faults->groupBy('fault_type')->map->count(),
            'by_status' => $faults->groupBy('status')->map->count(),
            'total_downtime_hours' => $faults->sum('downtime_minutes') / 60,
            'average_resolution_time' => $faults->whereNotNull('downtime_end')->avg('downtime_minutes'),
            'most_common_faults' => $faults->groupBy('fault_type')
                ->map->count()
                ->sortDesc()
                ->take(5)
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.faults', compact('faults', 'summary'));
            return $pdf->download('faults-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return view('reports.faults', compact('faults', 'summary'));
    }

    public function export($type)
    {
        // This would handle CSV/Excel exports
        // You can implement based on your preferred export library
    }
}