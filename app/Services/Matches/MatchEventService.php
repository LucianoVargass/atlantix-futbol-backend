<?php

namespace App\Services\Matches;

use App\Models\MatchEvent;

class MatchEventService
{
    public function list(array $filters = [])
    {
        return MatchEvent::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): MatchEvent
    {
        return MatchEvent::create($data);
    }

    public function update(MatchEvent $model, array $data): MatchEvent
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(MatchEvent $model): bool
    {
        return (bool) $model->delete();
    }
}
