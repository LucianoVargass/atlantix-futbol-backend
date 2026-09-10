<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Referee;
use App\Models\FootballMatch;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    /** Ficha del jugador logueado (si su usuario está vinculado a un Player). */
    public function player(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $player = Player::where('user_id', $user->id)
            ->with('team:id,name,logo_url')
            ->first();

        return response()->json(['data' => $player]);
    }

    /** Partidos asignados al árbitro logueado. */
    public function matches(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $refereeIds = Referee::where('user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                if ($user->email) {
                    $q->whereRaw('LOWER(email) = ?', [strtolower($user->email)]);
                }
            })
            ->pluck('id');

        $matches = FootballMatch::whereIn('referee_id', $refereeIds)
            ->with(['homeTeam:id,name,logo_url', 'awayTeam:id,name,logo_url', 'matchday:id,name,number', 'tournament:id,name'])
            ->orderByRaw('COALESCE(scheduled_at, created_at) desc')
            ->get();

        return response()->json(['data' => $matches]);
    }

    public function tournaments(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $assigned = $user->tournaments()->get();
        $owned = $user->ownedTournaments()->get();
        $tournaments = $assigned->merge($owned)->unique('id')->values();

        return response()->json([
            'data' => $tournaments,
        ]);
    }

    public function mercadoPago(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'data' => [
                'access_token' => $user->mp_access_token,
                'public_key' => $user->mp_public_key,
                'mode' => $user->mp_mode ?? 'sandbox',
                'notification_url' => $user->mp_notification_url,
            ],
        ]);
    }

    public function updateMercadoPago(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'access_token' => ['nullable', 'string', 'max:255'],
            'public_key' => ['nullable', 'string', 'max:255'],
            'mode' => ['nullable', 'in:sandbox,prod'],
            'notification_url' => ['nullable', 'url', 'max:255'],
        ]);

        $user->mp_access_token = $data['access_token'] ?? null;
        $user->mp_public_key = $data['public_key'] ?? null;
        $user->mp_mode = $data['mode'] ?? null;
        $user->mp_notification_url = $data['notification_url'] ?? null;
        $user->save();

        return response()->json([
            'data' => [
                'access_token' => $user->mp_access_token,
                'public_key' => $user->mp_public_key,
                'mode' => $user->mp_mode ?? 'sandbox',
                'notification_url' => $user->mp_notification_url,
            ],
        ]);
    }
}
