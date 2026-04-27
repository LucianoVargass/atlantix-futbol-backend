<?php

namespace App\Services\Matches;

use App\Models\Matchday;

class MatchdayService
{
    public function list(array $filters = [])
    {
        return Matchday::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Matchday
    {
        return Matchday::create($data);
    }

    public function update(Matchday $model, array $data): Matchday
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Matchday $model): bool
    {
        return (bool) $model->delete();
    }
}
