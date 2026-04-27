<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;

class MercadoPagoController extends Controller
{
    public function createPreference(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:255'],
            'payer_email' => ['nullable', 'email'],
            'external_reference' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'tournament_id' => ['nullable', 'exists:tournaments,id'],
            'success_url' => ['nullable', 'url'],
            'pending_url' => ['nullable', 'url'],
            'failure_url' => ['nullable', 'url'],
            'notification_url' => ['nullable', 'url'],
        ]);

        $token = null;
        $notificationOverride = null;
        if (!empty($data['tournament_id'])) {
            $tournament = Tournament::find($data['tournament_id']);
            if ($tournament?->admin_user_id) {
                $admin = $tournament->admin;
                $token = $admin?->mp_access_token ?: null;
                $notificationOverride = $admin?->mp_notification_url ?: null;
            }
        }

        if (!$token && $request->user()) {
            $token = $request->user()->mp_access_token ?: null;
            $notificationOverride = $request->user()->mp_notification_url ?: null;
        }

        $token = $token ?: config('mercadopago.access_token');
        if (!$token) {
            return response()->json(['message' => 'MERCADOPAGO_ACCESS_TOKEN no configurado'], 500);
        }

        SDK::setAccessToken($token);

        $item = new Item();
        $item->title = $data['title'];
        $item->quantity = 1;
        $item->unit_price = (float) $data['amount'];
        $item->currency_id = $data['currency'] ?? 'ARS';

        $preference = new Preference();
        $preference->items = [$item];
        $preference->external_reference = $data['external_reference'] ?? null;
        $preference->metadata = $data['metadata'] ?? null;

        if (!empty($data['payer_email'])) {
            $preference->payer = [
                'email' => $data['payer_email'],
            ];
        }

        $frontendUrl = rtrim(config('mercadopago.frontend_url') ?? config('app.url'), '/');
        $preference->back_urls = [
            'success' => $data['success_url'] ?? $frontendUrl . '/pagos/exito',
            'pending' => $data['pending_url'] ?? $frontendUrl . '/pagos/pendiente',
            'failure' => $data['failure_url'] ?? $frontendUrl . '/pagos/error',
        ];
        $preference->auto_return = 'approved';

        $notificationUrl = $data['notification_url'] ?? $notificationOverride ?? config('mercadopago.notification_url');
        if ($notificationUrl) {
            $preference->notification_url = $notificationUrl;
        }

        $preference->save();

        return response()->json([
            'id' => $preference->id,
            'init_point' => $preference->init_point,
            'sandbox_init_point' => $preference->sandbox_init_point,
        ]);
    }

    public function webhook(Request $request)
    {
        logger()->info('Mercado Pago webhook', [
            'payload' => $request->all(),
        ]);

        return response()->json(['received' => true]);
    }
}
