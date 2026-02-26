<?php
namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Support\Collection;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;
    public function create(array $data): Invoice;
    public function getByContractId(int $contractId): Collection;
    public function getNextSequence(int $tenantId): int;
    public function paginateByContract(
        int $contractId,
        array $filters,
        int $perPage = 10
    );

    public function findWithRelations(int $id, array $relations = []);
    public function update(int $id, array $data): Invoice;
}