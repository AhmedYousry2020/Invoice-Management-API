<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'paid_at' => $this->paid_at,
            'remaining_balance' => $this->total - $this->payments->sum('amount'),
            'contract' => $this->whenLoaded('contract', fn () => new ContractResource($this->contract)),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}