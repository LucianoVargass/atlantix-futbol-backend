<?php

namespace App\Services\Tournaments;

use App\Models\TournamentNews;

class TournamentNewsService
{
    public function list(array $filters = [])
    {
        return TournamentNews::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentNews
    {
        return TournamentNews::create($data);
    }

    public function update(TournamentNews $model, array $data): TournamentNews
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentNews $model): bool
    {
        return (bool) $model->delete();
    }
}
