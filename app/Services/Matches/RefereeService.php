<?php

namespace App\Services\Matches;

use App\Models\Referee;

class RefereeService
{
    public function list(array $filters = [])
    {
        return Referee::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Referee
    {
        return Referee::create($data);
    }

    public function update(Referee $model, array $data): Referee
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Referee $model): bool
    {
        return (bool) $model->delete();
    }
}
