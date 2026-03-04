<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;
use App\Models\WorkOrder;
use App\Models\User;
use App\Policies\AssetPolicy;
use App\Policies\FaultPolicy;
use App\Policies\MaintenanceSchedulePolicy;
use App\Policies\WorkOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Asset::class => AssetPolicy::class,
        Fault::class => FaultPolicy::class,
        MaintenanceSchedule::class => MaintenanceSchedulePolicy::class,
        WorkOrder::class => WorkOrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates
        Gate::define('delete-users', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-audit-logs', function ($user) {
            return $user->hasRole('admin');
        });
        
        Gate::define('verify-work-orders', function ($user) {
            return $user->hasRole(['admin', 'maintenance-supervisor']);
        });
    }
}