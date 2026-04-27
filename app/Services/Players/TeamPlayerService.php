<?php

namespace App\Services\Players;

use App\Models\TeamPlayer;

class TeamPlayerService
{
    public function list(array $filters = [])
    {
        return TeamPlayer::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TeamPlayer
    {
        return TeamPlayer::create($data);
    }

    public function update(TeamPlayer $model, array $data): TeamPlayer
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TeamPlayer $model): bool
    {
        return (bool) $model->delete();
    }
}
