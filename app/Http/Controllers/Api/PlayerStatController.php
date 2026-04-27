<?php

namespace App\Http\Controllers\Api;

use App\Models\PlayerStat;

class PlayerStatController extends BaseApiController
{
    protected string $modelClass = PlayerStat::class;
}
