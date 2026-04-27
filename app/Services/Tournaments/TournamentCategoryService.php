<?php

namespace App\Services\Tournaments;

use App\Models\TournamentCategory;

class TournamentCategoryService
{
    public function list(array $filters = [])
    {
        return TournamentCategory::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentCategory
    {
        return TournamentCategory::create($data);
    }

    public function update(TournamentCategory $model, array $data): TournamentCategory
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentCategory $model): bool
    {
        return (bool) $model->delete();
    }
}
