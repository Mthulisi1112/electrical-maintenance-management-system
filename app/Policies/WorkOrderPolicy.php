<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        
        // All authenticated users can access the index page
        // But controller will handle filtering
        return true;
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->id === $workOrder->technician_id || 
               $user->id === $workOrder->supervisor_id ||
               $user->hasRole(['admin', 'auditor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor']);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('maintenance-supervisor') && in_array($workOrder->status, ['pending', 'completed'])) {
            return true;
        }

        if ($user->hasRole('technician') && $workOrder->technician_id === $user->id && 
            in_array($workOrder->status, ['pending', 'in_progress'])) {
            return true;
        }

        return false;
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can verify ANY work order (for dashboard).
     */
    public function verifyAny(User $user): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor']);
    }

    /**
     * Determine if the user can verify a specific work order.
     */
    public function verify(User $user, WorkOrder $workOrder): bool
    {
        return $this->verifyAny($user) && $workOrder->status === 'completed';
    }

    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasRole('admin');
    }
}