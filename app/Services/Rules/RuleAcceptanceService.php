<?php

namespace App\Services\Rules;

use App\Models\RuleAcceptance;

class RuleAcceptanceService
{
    public function list(array $filters = [])
    {
        return RuleAcceptance::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): RuleAcceptance
    {
        return RuleAcceptance::create($data);
    }

    public function update(RuleAcceptance $model, array $data): RuleAcceptance
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(RuleAcceptance $model): bool
    {
        return (bool) $model->delete();
    }
}
