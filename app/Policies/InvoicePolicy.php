<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Contract;
use App\Models\Invoice;

class InvoicePolicy
{
    public function create(User $user, Contract $contract): bool
    {
        return $user->tenant_id === $contract->tenant_id;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->contract->tenant_id;
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->contract->tenant_id
            && $invoice->status !== 'cancelled';
    }
}