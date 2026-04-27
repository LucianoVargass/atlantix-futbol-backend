<?php

namespace App\Services\Stats;

use App\Models\TeamStat;

class TeamStatService
{
    public function list(array $filters = [])
    {
        return TeamStat::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TeamStat
    {
        return TeamStat::create($data);
    }

    public function update(TeamStat $model, array $data): TeamStat
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TeamStat $model): bool
    {
        return (bool) $model->delete();
    }
}
