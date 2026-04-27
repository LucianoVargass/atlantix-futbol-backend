<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Tournament $tournament): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'tournament_admin'], true);
    }

    public function update(User $user, Tournament $tournament): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role !== 'tournament_admin') return false;
        return $tournament->admin_user_id === $user->id
            || $tournament->admins()->where('users.id', $user->id)->exists();
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $this->update($user, $tournament);
    }
}
