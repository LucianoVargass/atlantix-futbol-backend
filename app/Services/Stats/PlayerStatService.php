<?php

namespace App\Services\Stats;

use App\Models\PlayerStat;

class PlayerStatService
{
    public function list(array $filters = [])
    {
        return PlayerStat::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): PlayerStat
    {
        return PlayerStat::create($data);
    }

    public function update(PlayerStat $model, array $data): PlayerStat
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(PlayerStat $model): bool
    {
        return (bool) $model->delete();
    }
}
