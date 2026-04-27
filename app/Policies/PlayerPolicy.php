<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;

class PlayerPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Player $player): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'tournament_admin', 'team_admin'], true);
    }

    public function update(User $user, Player $player): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'tournament_admin') return true;
        if ($user->role === 'team_admin') {
            if (!$player->team) return false;
            return $player->team->admins()->where('users.id', $user->id)->exists();
        }
        return false;
    }

    public function delete(User $user, Player $player): bool
    {
        return $this->update($user, $player);
    }
}
