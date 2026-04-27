<?php

namespace App\Services\Tournaments;

use App\Models\TournamentSetting;

class TournamentSettingService
{
    public function list(array $filters = [])
    {
        return TournamentSetting::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): TournamentSetting
    {
        return TournamentSetting::create($data);
    }

    public function update(TournamentSetting $model, array $data): TournamentSetting
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(TournamentSetting $model): bool
    {
        return (bool) $model->delete();
    }
}
