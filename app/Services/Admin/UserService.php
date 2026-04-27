<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list(array $filters = [])
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%");
                });
            })
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
