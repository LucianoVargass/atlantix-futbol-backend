<?php

namespace App\Services\Players;

use App\Models\TournamentPlayer;

class TournamentPlayerService
{
    public function list(array $filters = [])
    {
        return TournamentPlayer::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentPlayer
    {
        return TournamentPlayer::create($data);
    }

    public function update(TournamentPlayer $model, array $data): TournamentPlayer
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentPlayer $model): bool
    {
        return (bool) $model->delete();
    }
}
