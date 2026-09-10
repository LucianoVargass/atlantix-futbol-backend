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
        // miembro de la organización dueña del torneo (owner o admin),
        // o co-admin del torneo por el pivot legacy.
        return $tournament->managedBy($user->id);
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $this->update($user, $tournament);
    }
}
