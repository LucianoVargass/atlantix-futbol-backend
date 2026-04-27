<?php

namespace App\Services\Teams;

use App\Models\TeamAdmin;

class TeamAdminService
{
    public function list(array $filters = [])
    {
        return TeamAdmin::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TeamAdmin
    {
        return TeamAdmin::create($data);
    }

    public function update(TeamAdmin $model, array $data): TeamAdmin
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TeamAdmin $model): bool
    {
        return (bool) $model->delete();
    }
}
