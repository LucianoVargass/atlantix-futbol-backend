<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends BaseApiController
{
    use AuthorizesRequests;

    protected string $modelClass = Team::class;

    public function store(Request $request)
    {
        $this->authorize('create', Team::class);
        return parent::store($request);
    }

    public function update(Request $request, string $id)
    {
        $record = Team::findOrFail($id);
        $this->authorize('update', $record);
        $data = $this->validateRequest($request, true);
        $record->fill($data);
        $record->save();

        return response()->json($record);
    }

    public function destroy(string $id)
    {
        $record = Team::findOrFail($id);
        $this->authorize('delete', $record);
        $record->delete();

        return response()->json(['deleted' => true]);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'founded_year' => 'nullable|integer|min:1800|max:3000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'logo_url' => 'nullable|url',
        ];
    }
}
