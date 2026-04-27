<?php

namespace App\Services\Tournaments;

use App\Models\PhaseTeam;

class PhaseTeamService
{
    public function list(array $filters = [])
    {
        return PhaseTeam::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): PhaseTeam
    {
        return PhaseTeam::create($data);
    }

    public function update(PhaseTeam $model, array $data): PhaseTeam
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(PhaseTeam $model): bool
    {
        return (bool) $model->delete();
    }
}
