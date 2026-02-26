<?php
namespace App\DTOs;

use App\Http\Requests\StoreInvoiceRequest;

class CreateInvoiceDTO
{
    public function __construct(
        public readonly int $contract_id,
        public readonly string $due_date,
        public readonly int $tenant_id,
    ) {}

    public static function fromRequest(StoreInvoiceRequest $request): self
    {
        return new self(
            contract_id: $request->validated('contract_id'),
            due_date: $request->validated('due_date'),
            tenant_id: $request->user()->tenant_id,
        );
    }
}