<?php

namespace App\Services\Stats;

use App\Models\Sanction;

class SanctionService
{
    public function list(array $filters = [])
    {
        return Sanction::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Sanction
    {
        return Sanction::create($data);
    }

    public function update(Sanction $model, array $data): Sanction
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Sanction $model): bool
    {
        return (bool) $model->delete();
    }
}
