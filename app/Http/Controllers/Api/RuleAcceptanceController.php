<?php

namespace App\Http\Controllers\Api;

use App\Models\RuleAcceptance;
use Illuminate\Http\Request;

class RuleAcceptanceController extends BaseApiController
{
    protected string $modelClass = RuleAcceptance::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'rules_version_id' => 'required|integer|exists:rules_versions,id',
            'accepted_at' => 'nullable|date',
            'ip_address' => 'nullable|string|max:255',
        ];
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $this->validateRequest($request, false);
        $data['user_id'] = $user->id;
        $data['accepted_at'] = $data['accepted_at'] ?? now();
        $data['ip_address'] = $data['ip_address'] ?? $request->ip();

        $record = RuleAcceptance::create($data);

        return response()->json($record, 201);
    }
}
