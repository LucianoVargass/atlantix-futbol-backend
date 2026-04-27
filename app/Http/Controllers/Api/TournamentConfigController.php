<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentSetting;
use App\Models\RulesVersion;
use App\Models\TournamentTerm;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TournamentConfigController extends Controller
{
    use AuthorizesRequests;

    public function settings(Tournament $tournament)
    {
        $settings = TournamentSetting::firstOrCreate(
            ['tournament_id' => $tournament->id],
            ['settings' => []]
        );

        return response()->json(['data' => $settings]);
    }

    public function updateSettings(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);
        $data = $request->validate([
            'settings' => ['required', 'array'],
        ]);

        $settings = TournamentSetting::updateOrCreate(
            ['tournament_id' => $tournament->id],
            ['settings' => $data['settings']]
        );

        return response()->json(['data' => $settings]);
    }

    public function rules(Tournament $tournament)
    {
        $rules = RulesVersion::where('tournament_id', $tournament->id)
            ->orderByDesc('version')
            ->first();

        return response()->json(['data' => $rules]);
    }

    public function updateRules(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);
        $data = $request->validate([
            'rules' => ['required', 'array'],
        ]);

        $currentVersion = RulesVersion::where('tournament_id', $tournament->id)
            ->max('version');
        $nextVersion = $currentVersion ? $currentVersion + 1 : 1;

        $rules = RulesVersion::create([
            'tournament_id' => $tournament->id,
            'version' => $nextVersion,
            'rules' => $data['rules'],
            'created_by' => $request->user()?->id,
        ]);

        $tournament->update([
            'rules_version' => (string) $nextVersion,
            'rules' => $data['rules'],
        ]);

        return response()->json(['data' => $rules]);
    }

    public function terms(Tournament $tournament)
    {
        $terms = TournamentTerm::where('tournament_id', $tournament->id)
            ->orderByDesc('version')
            ->get();

        return response()->json(['data' => $terms]);
    }

    public function updateTerms(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);
        $data = $request->validate([
            'terms' => ['required', 'array'],
            'terms.*.title' => ['required', 'string', 'max:255'],
            'terms.*.body' => ['required', 'string'],
            'terms.*.required' => ['nullable', 'boolean'],
        ]);

        $currentVersion = TournamentTerm::where('tournament_id', $tournament->id)
            ->max('version');
        $nextVersion = $currentVersion ? $currentVersion + 1 : 1;

        TournamentTerm::where('tournament_id', $tournament->id)->delete();

        foreach ($data['terms'] as $term) {
            TournamentTerm::create([
                'tournament_id' => $tournament->id,
                'title' => $term['title'],
                'body' => $term['body'],
                'required' => $term['required'] ?? true,
                'version' => $nextVersion,
            ]);
        }

        $terms = TournamentTerm::where('tournament_id', $tournament->id)->get();

        return response()->json(['data' => $terms]);
    }
}
