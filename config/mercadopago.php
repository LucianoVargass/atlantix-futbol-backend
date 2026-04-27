<?php

return [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'notification_url' => env('MERCADOPAGO_NOTIFICATION_URL'),
    'frontend_url' => env('MERCADOPAGO_FRONTEND_URL', env('APP_URL')),
];
