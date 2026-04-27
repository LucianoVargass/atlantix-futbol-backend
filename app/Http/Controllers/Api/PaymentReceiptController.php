<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentReceipt;

class PaymentReceiptController extends BaseApiController
{
    protected string $modelClass = PaymentReceipt::class;
}
