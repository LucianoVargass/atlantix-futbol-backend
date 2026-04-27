<?php

namespace App\Services\Matches;

use App\Models\MatchLineup;

class MatchLineupService
{
    public function list(array $filters = [])
    {
        return MatchLineup::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): MatchLineup
    {
        return MatchLineup::create($data);
    }

    public function update(MatchLineup $model, array $data): MatchLineup
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(MatchLineup $model): bool
    {
        return (bool) $model->delete();
    }
}
