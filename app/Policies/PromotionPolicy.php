<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Promotion $promotion): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $promotion->campaign->supplier->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Promotion $promotion): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $promotion->campaign->supplier->users->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Promotion $promotion): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $promotion->campaign->supplier->users->contains($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Promotion $promotion): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $promotion->campaign->supplier->users->contains($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Promotion $promotion): bool
    {
        return false;
    }
}
