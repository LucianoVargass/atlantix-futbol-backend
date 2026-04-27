<?php

namespace App\Services\Teams;

use App\Models\Team;

class TeamService
{
    public function list(array $filters = [])
    {
        return Team::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(Team $model, array $data): Team
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Team $model): bool
    {
        return (bool) $model->delete();
    }
}
