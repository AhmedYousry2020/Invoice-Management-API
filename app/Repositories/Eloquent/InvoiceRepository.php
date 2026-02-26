<?php
namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice
    {
        return Invoice::with(['payments', 'contract'])->find($id);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function getByContractId(int $contractId): Collection
    {
        return Invoice::with('payments')
            ->where('contract_id', $contractId)
            ->get();
    }

    public function getNextSequence(int $tenantId): int
    {
        $lastInvoice = Invoice::where('tenant_id', $tenantId)
            ->latest('id')
            ->first();

        if (!$lastInvoice) {
            return 1;
        }
        return intval(substr($lastInvoice->invoice_number, -4)) + 1;
    }
    public function paginateByContract(
    int $contractId,
    array $filters,
    int $perPage = 10
    ) {
        $query = Invoice::query()
            ->where('contract_id', $contractId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('due_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('due_date', '<=', $filters['to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        return Invoice::with($relations)->findOrFail($id);
    }
    public function update(int $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update($data);

        return $invoice->refresh();
    }
}