<?php

namespace App\Policies;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any integrations.
     */
    public function viewAny(User $user): bool
    {
        // Allow if user is admin or has appropriate role
        return $user->is_admin || $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the integration.
     */
    public function view(User $user, Integration $integration): bool
    {
        // Allow if user is admin or owns the integration
        return $user->is_admin || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create integrations.
     */
    public function create(User $user): bool
    {
        // Allow if user is admin
        return $user->is_admin || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the integration.
     */
    public function update(User $user, Integration $integration): bool
    {
        // Allow if user is admin
        return $user->is_admin || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the integration.
     */
    public function delete(User $user, Integration $integration): bool
    {
        // Allow if user is admin
        return $user->is_admin || $user->role === 'admin';
    }
}
