<?php

namespace App\Services\Payments;

use App\Models\PaymentReceipt;

class PaymentReceiptService
{
    public function list(array $filters = [])
    {
        return PaymentReceipt::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): PaymentReceipt
    {
        return PaymentReceipt::create($data);
    }

    public function update(PaymentReceipt $model, array $data): PaymentReceipt
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(PaymentReceipt $model): bool
    {
        return (bool) $model->delete();
    }
}
