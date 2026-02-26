<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'contract_id' => $this->contract_id,
            'total_invoiced' => $this->total_invoiced,
            'total_paid' => $this->total_paid,
            'outstanding_balance' => $this->outstanding_balance,
            'invoices_count' => $this->invoices_count,
            'latest_invoice_date' => $this->latest_invoice_date?->format('Y-m-d'),
        ];
    }
}