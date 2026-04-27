<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function teamLogo(Request $request, Team $team)
    {
        $request->validate([
            'logo' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $path = $request->file('logo')->store("teams/{$team->id}", 'public');
        $team->update(['logo_url' => Storage::url($path)]);

        return response()->json([
            'data' => $team,
        ]);
    }

    public function playerPhoto(Request $request, Player $player)
    {
        // Primero logueamos todo lo que llega, sin acceder a métodos del archivo
        \Log::info('Subida de foto jugador', [
            'player_id' => $player->id,
            'all' => $request->all(),
            'files' => $request->allFiles(),
            'hasFile' => $request->hasFile('photo'),
            'file_class' => is_object($request->file('photo')) ? get_class($request->file('photo')) : null,
        ]);

        if (!$request->hasFile('photo') || !$request->file('photo')) {
            \Log::error('No se recibió archivo en el campo photo', [
                'all' => $request->all(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all(),
            ]);
            return response()->json(['error' => 'No se recibió archivo'], 422);
        }

        // Ahora sí, accedemos a métodos del archivo
        \Log::info('Archivo recibido', [
            'size' => $request->file('photo')->getSize(),
            'mime' => $request->file('photo')->getMimeType(),
        ]);

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:10240'], // 10MB
        ]);

        $path = $request->file('photo')->store("players/{$player->id}", 'public');
        $player->update(['profile_photo' => Storage::url($path)]);

        return response()->json([
            'data' => $player,
        ]);
    }

    public function playerDocuments(Request $request, Player $player)
    {
        $data = $request->validate([
            'document_type' => ['nullable', 'string'],
            'document_number' => ['nullable', 'string'],
            'front' => ['nullable', 'file', 'image', 'max:10240'], // 10MB
            'back' => ['nullable', 'file', 'image', 'max:10240'], // 10MB
        ]);

        $document = PlayerDocument::firstOrCreate(
            ['player_id' => $player->id],
            ['status' => 'pending']
        );

        if ($request->hasFile('front')) {
            $frontPath = $request->file('front')->store("players/{$player->id}/documents", 'public');
            $data['front_url'] = Storage::url($frontPath);
        }

        if ($request->hasFile('back')) {
            $backPath = $request->file('back')->store("players/{$player->id}/documents", 'public');
            $data['back_url'] = Storage::url($backPath);
        }

        $document->update([
            'document_type' => $data['document_type'] ?? $document->document_type,
            'document_number' => $data['document_number'] ?? $document->document_number,
            'front_url' => $data['front_url'] ?? $document->front_url,
            'back_url' => $data['back_url'] ?? $document->back_url,
        ]);

        return response()->json([
            'data' => $document,
        ]);
    }

    public function tournamentImage(Request $request, Tournament $tournament)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store("tournaments/{$tournament->id}", 'public');
        $tournament->update(['image_url' => Storage::url($path)]);

        return response()->json([
            'data' => $tournament,
        ]);
    }
}
