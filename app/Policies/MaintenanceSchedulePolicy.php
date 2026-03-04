<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MaintenanceSchedule;

class MaintenanceSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor']);
    }

    public function update(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor']);
    }

    public function delete(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasRole('admin');
    }
}