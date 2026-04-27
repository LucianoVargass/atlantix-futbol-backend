<?php

namespace App\Services\Players;

use App\Models\PlayerDocument;

class PlayerDocumentService
{
    public function list(array $filters = [])
    {
        return PlayerDocument::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): PlayerDocument
    {
        return PlayerDocument::create($data);
    }

    public function update(PlayerDocument $model, array $data): PlayerDocument
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(PlayerDocument $model): bool
    {
        return (bool) $model->delete();
    }
}
