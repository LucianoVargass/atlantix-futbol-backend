<?php

namespace App\Services\Tournaments;

use App\Models\TournamentPhase;

class TournamentPhaseService
{
    public function list(array $filters = [])
    {
        return TournamentPhase::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentPhase
    {
        return TournamentPhase::create($data);
    }

    public function update(TournamentPhase $model, array $data): TournamentPhase
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentPhase $model): bool
    {
        return (bool) $model->delete();
    }
}
