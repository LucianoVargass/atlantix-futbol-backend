<?php

namespace App\Services\Payments;

use App\Models\Payment;

class PaymentService
{
    public function list(array $filters = [])
    {
        return Payment::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $model, array $data): Payment
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Payment $model): bool
    {
        return (bool) $model->delete();
    }
}
