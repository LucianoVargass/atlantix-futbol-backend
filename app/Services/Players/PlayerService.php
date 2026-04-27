<?php

namespace App\Services\Players;

use App\Models\Player;

class PlayerService
{
    public function list(array $filters = [])
    {
        return Player::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Player
    {
        return Player::create($data);
    }

    public function update(Player $model, array $data): Player
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Player $model): bool
    {
        return (bool) $model->delete();
    }
}
