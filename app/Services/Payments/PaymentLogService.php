<?php

namespace App\Services\Payments;

use App\Models\PaymentLog;

class PaymentLogService
{
    public function list(array $filters = [])
    {
        return PaymentLog::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): PaymentLog
    {
        return PaymentLog::create($data);
    }

    public function update(PaymentLog $model, array $data): PaymentLog
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(PaymentLog $model): bool
    {
        return (bool) $model->delete();
    }
}
