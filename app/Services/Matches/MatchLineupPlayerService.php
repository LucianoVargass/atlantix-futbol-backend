<?php

namespace App\Services\Matches;

use App\Models\MatchLineupPlayer;

class MatchLineupPlayerService
{
    public function list(array $filters = [])
    {
        return MatchLineupPlayer::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): MatchLineupPlayer
    {
        return MatchLineupPlayer::create($data);
    }

    public function update(MatchLineupPlayer $model, array $data): MatchLineupPlayer
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(MatchLineupPlayer $model): bool
    {
        return (bool) $model->delete();
    }
}
