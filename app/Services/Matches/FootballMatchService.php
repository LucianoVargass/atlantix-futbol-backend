<?php

namespace App\Services\Matches;

use App\Models\FootballMatch;

class FootballMatchService
{
    public function list(array $filters = [])
    {
        return FootballMatch::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): FootballMatch
    {
        return FootballMatch::create($data);
    }

    public function update(FootballMatch $model, array $data): FootballMatch
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(FootballMatch $model): bool
    {
        return (bool) $model->delete();
    }
}
