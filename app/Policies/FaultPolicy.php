<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Fault;

class FaultPolicy
{
    public function viewAny(User $user): bool
    {
         // All authenticated users can access the index page
        return true;
    }

   public function view(User $user, Fault $fault): bool
    {
        // Admin, supervisor, auditor can view all
        if ($user->hasRole(['admin', 'maintenance-supervisor', 'auditor'])) {
            return true;
        }
        
        // Technicians can only view faults they reported or are assigned to
        return $user->id === $fault->reported_by || $user->id === $fault->assigned_to;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor', 'technician']);
    }

    public function update(User $user, Fault $fault): bool
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('maintenance-supervisor') && !$fault->isResolved());
    }

    public function delete(User $user, Fault $fault): bool
    {
        return $user->hasRole('admin');
    }

    public function assign(User $user, Fault $fault): bool
    {
        return $user->hasRole(['admin', 'maintenance-supervisor']) && 
               in_array($fault->status, ['reported', 'investigating']);
    }

    public function resolve(User $user, Fault $fault): bool
    {
        return ($user->id === $fault->assigned_to || $user->hasRole(['admin', 'maintenance-supervisor'])) &&
               !in_array($fault->status, ['resolved', 'closed']);
    }

    public function restore(User $user, Fault $fault): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Fault $fault): bool
    {
        return $user->hasRole('admin');
    }
}