<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Team $team): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'tournament_admin', 'team_admin'], true);
    }

    public function update(User $user, Team $team): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'tournament_admin') return true;
        if ($user->role === 'team_admin') {
            return (int) $team->owner_user_id === (int) $user->id
                || $team->admins()->where('users.id', $user->id)->exists();
        }
        return false;
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }
}
