<?php

$envOrigins = env('CORS_ALLOWED_ORIGINS');
$allowedOrigins = ['*'];

if ($envOrigins) {
    $trimmed = trim($envOrigins);
    if ($trimmed === '*') {
        $allowedOrigins = ['*'];
    } else {
        $allowedOrigins = array_filter(array_map('trim', explode(',', $trimmed)));
    }
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
