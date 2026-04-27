<?php

namespace App\Services\Rules;

use App\Models\RulesVersion;

class RulesVersionService
{
    public function list(array $filters = [])
    {
        return RulesVersion::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): RulesVersion
    {
        return RulesVersion::create($data);
    }

    public function update(RulesVersion $model, array $data): RulesVersion
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(RulesVersion $model): bool
    {
        return (bool) $model->delete();
    }
}
