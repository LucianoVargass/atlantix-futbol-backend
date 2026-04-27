<?php

namespace App\Models\Concerns;

trait HasApiTokensOptional
{
    public function createToken(string $name, array $abilities = ['*'])
    {
        if (class_exists(\Laravel\Sanctum\HasApiTokens::class)) {
            // When Sanctum is installed, the real trait should be used.
            // This fallback keeps the app running before installation.
        }

        return new class {
            public string $plainTextToken = '';
        };
    }
}
