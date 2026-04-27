<?php

namespace App\Services\Tournaments;

use App\Models\TournamentAdmin;

class TournamentAdminService
{
    public function list(array $filters = [])
    {
        return TournamentAdmin::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentAdmin
    {
        return TournamentAdmin::create($data);
    }

    public function update(TournamentAdmin $model, array $data): TournamentAdmin
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentAdmin $model): bool
    {
        return (bool) $model->delete();
    }
}
