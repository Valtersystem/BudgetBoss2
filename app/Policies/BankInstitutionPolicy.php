<?php

namespace App\Policies;

use App\Models\BankInstitution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BankInstitutionPolicy
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
    public function view(User $user, BankInstitution $bankInstitution): bool
    {
        return $user->id === $bankInstitution->user_id;
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
    public function update(User $user, BankInstitution $bankInstitution): bool
    {
        return $user->id === $bankInstitution->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BankInstitution $bankInstitution): bool
    {
        return $user->id === $bankInstitution->user_id;
    }
}
