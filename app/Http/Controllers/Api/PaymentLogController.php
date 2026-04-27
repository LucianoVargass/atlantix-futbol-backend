<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentLog;

class PaymentLogController extends BaseApiController
{
    protected string $modelClass = PaymentLog::class;
}
