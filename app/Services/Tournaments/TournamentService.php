<?php

namespace App\Services\Tournaments;

use App\Models\Tournament;

class TournamentService
{
    public function list(array $filters = [])
    {
        return Tournament::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Tournament
    {
        return Tournament::create($data);
    }

    public function update(Tournament $model, array $data): Tournament
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Tournament $model): bool
    {
        return (bool) $model->delete();
    }
}
