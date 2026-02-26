<?php
namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::with('invoice')->find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function getByInvoiceId(int $invoiceId): array
    {
        return Payment::where('invoice_id', $invoiceId)
            ->orderBy('paid_at', 'asc')
            ->get()
            ->toArray();
    }
    public function sumByInvoiceId(int $invoiceId): float
    {
        return Payment::where('invoice_id', $invoiceId)->sum('amount');
    }
}