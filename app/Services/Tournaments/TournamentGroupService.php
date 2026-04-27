<?php

namespace App\Services\Tournaments;

use App\Models\TournamentGroup;

class TournamentGroupService
{
    public function list(array $filters = [])
    {
        return TournamentGroup::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentGroup
    {
        return TournamentGroup::create($data);
    }

    public function update(TournamentGroup $model, array $data): TournamentGroup
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentGroup $model): bool
    {
        return (bool) $model->delete();
    }
}
