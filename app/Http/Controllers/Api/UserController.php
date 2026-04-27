<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseApiController
{
    protected string $modelClass = User::class;

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, false);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $record = User::create($data);

        return response()->json($record, 201);
    }

    public function update(Request $request, string $id)
    {
        $record = User::findOrFail($id);
        $data = $this->validateRequest($request, true);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $record->fill($data);
        $record->save();

        return response()->json($record);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => $isUpdate ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'avatar_url' => 'nullable|url',
            'mp_access_token' => 'nullable|string|max:255',
            'mp_public_key' => 'nullable|string|max:255',
            'mp_mode' => 'nullable|string|in:sandbox,prod',
            'mp_notification_url' => 'nullable|url|max:255',
        ];
    }
}
