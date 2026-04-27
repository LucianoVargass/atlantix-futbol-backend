<?php

namespace App\Http\Controllers\Api;

use App\Models\TournamentNews;
use Illuminate\Http\Request;

class TournamentNewsController extends BaseApiController
{
    protected string $modelClass = TournamentNews::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, false);
        if (!array_key_exists('published_at', $data) && ($data['is_published'] ?? false)) {
            $data['published_at'] = now();
        }

        $record = TournamentNews::create($data);

        return response()->json($record, 201);
    }
}
