<?php

namespace App\Services\Teams;

use App\Models\TeamTournamentRegistration;

class TeamTournamentRegistrationService
{
    public function list(array $filters = [])
    {
        return TeamTournamentRegistration::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TeamTournamentRegistration
    {
        return TeamTournamentRegistration::create($data);
    }

    public function update(TeamTournamentRegistration $model, array $data): TeamTournamentRegistration
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TeamTournamentRegistration $model): bool
    {
        return (bool) $model->delete();
    }
}
