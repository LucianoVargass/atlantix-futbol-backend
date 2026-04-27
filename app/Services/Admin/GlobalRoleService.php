<?php

namespace App\Services\Admin;

use App\Models\GlobalRole;

class GlobalRoleService
{
    public function list(array $filters = [])
    {
        return GlobalRole::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): GlobalRole
    {
        return GlobalRole::create($data);
    }

    public function update(GlobalRole $globalRole, array $data): GlobalRole
    {
        $globalRole->update($data);
        return $globalRole->refresh();
    }

    public function delete(GlobalRole $globalRole): bool
    {
        return (bool) $globalRole->delete();
    }
}
