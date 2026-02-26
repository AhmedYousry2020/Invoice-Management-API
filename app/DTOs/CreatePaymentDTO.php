<?php
namespace App\DTOs;

use App\Http\Requests\StorePaymentRequest;

class CreatePaymentDTO
{
    public function __construct(
        public readonly int $invoice_id,
        public readonly float $amount,
        public readonly string $payment_method,
        public readonly ?string $reference_number,
        public readonly int $tenant_id,

    ) {}

    public static function fromRequest(StorePaymentRequest $request): self
    {
        return new self(
            invoice_id: $request->validated('invoice_id'),
            amount: $request->validated('amount'),
            payment_method: $request->validated('payment_method'),
            reference_number: $request->validated('reference_number') ?? null,
            tenant_id: $request->user()->tenant_id,
        );
    }
}