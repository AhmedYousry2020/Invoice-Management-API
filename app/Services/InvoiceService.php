<?php

namespace App\Services;

use App\DTOs\CreateInvoiceDTO;
use App\DTOs\CreatePaymentDTO;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Taxes\TaxService;
use App\Models\Invoice;
use App\Models\Payment;
use App\Enums\InvoiceStatus;
use App\Enums\ContractStatus;
use App\Exceptions\ContractNotActiveException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvoiceCancelledException;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private ContractRepositoryInterface $contractRepo,
        private InvoiceRepositoryInterface $invoiceRepo,
        private PaymentRepositoryInterface $paymentRepo,
        private TaxService $taxService,
    ) {}

    /**
     * Create Invoice
     */
    public function createInvoice(CreateInvoiceDTO $dto): Invoice
    {
        $contract = $this->contractRepo->findById($dto->contract_id);

        if (!$contract || $contract->status !== ContractStatus::Active) {
            throw new ContractNotActiveException();
        }

        if ($contract->tenant_id !== $dto->tenant_id) {
            throw new ContractNotActiveException();
        }

        return DB::transaction(function () use ($contract, $dto) {

            $subtotal = $contract->rent_amount;

            $taxAmount = $this->taxService->calculateTotal($subtotal);

            $total = $subtotal + $taxAmount;

            $invoiceNumber = $this->generateInvoiceNumber($dto->tenant_id);

            return $this->invoiceRepo->create([
                'tenant_id'      => $dto->tenant_id,
                'contract_id'    => $dto->contract_id,
                'invoice_number' => $invoiceNumber,
                'subtotal'       => $subtotal,
                'tax_amount'     => $taxAmount,
                'total'          => $total,
                'status'         => InvoiceStatus::Pending,
                'due_date'       => $dto->due_date,
            ]);
        });
    }

    /**
     * Record Payment
     */
    public function recordPayment(CreatePaymentDTO $dto): Payment
    {
        $invoice = $this->invoiceRepo->findById($dto->invoice_id);

        if (!$invoice) {
            throw new \Exception('Invoice not found.');
        }

        if ($invoice->status === InvoiceStatus::Cancelled) {
            throw new InvoiceCancelledException();
        }

        $paidAmount = $this->paymentRepo->sumByInvoiceId($dto->invoice_id);

        $remainingBalance = $invoice->total - $paidAmount;

        if ($dto->amount > $remainingBalance) {
            throw new InsufficientBalanceException();
        }

        return DB::transaction(function () use ($invoice, $dto, $paidAmount) {

            $payment = $this->paymentRepo->create([
                'invoice_id'       => $dto->invoice_id,
                'tenant_id'        => $dto->tenant_id,
                'amount'           => $dto->amount,
                'payment_method'   => $dto->payment_method,
                'reference_number' => $dto->reference_number,
                'paid_at'          => now(),
            ]);

            $newTotalPaid = $paidAmount + $dto->amount;

            if ($newTotalPaid >= $invoice->total) {
                $invoice->status = InvoiceStatus::Paid;
                $invoice->paid_at = now();
            } elseif ($newTotalPaid > 0) {
                $invoice->status = InvoiceStatus::PartiallyPaid;
                $invoice->paid_at = null;
            }

            $this->invoiceRepo->update($invoice->id, [
                'status'  => $invoice->status,
                'paid_at' => $invoice->paid_at,
            ]);

            return $payment;
        });
    }

    /**
     * Contract Financial Summary
     */
    public function getContractSummary(int $contractId): array
    {
        $invoices = $this->invoiceRepo->getByContractId($contractId);

        $totalInvoiced = $invoices->sum('total');

        $totalPaid = $invoices->sum(function ($invoice) {
            return $this->paymentRepo->sumByInvoiceId($invoice->id);
        });

        return [
            'contract_id'       => $contractId,
            'total_invoiced'    => $totalInvoiced,
            'total_paid'        => $totalPaid,
            'outstanding_balance' => $totalInvoiced - $totalPaid,
            'invoices_count'    => $invoices->count(),
            'latest_invoice_date' => $invoices->max('created_at'),
        ];
    }

    /**
     * Invoice Number Generator
     */
    private function generateInvoiceNumber(int $tenantId): string
    {
        $sequence = $this->invoiceRepo->getNextSequence($tenantId);

        return sprintf(
            "INV%03d-%s-%04d",
            $tenantId,
            now()->format('Ym'),
            $sequence
        );
    }

    public function listInvoices(
    int $contractId,
    array $filters,
    int $perPage = 10
    ) {

        return $this->invoiceRepo->paginateByContract(
            $contractId,
            $filters,
            $perPage
        );
    }

    public function getInvoiceDetails(int $invoiceId): Invoice
    {
        return $this->invoiceRepo->findWithRelations(
            $invoiceId,
            ['payments', 'contract']
        );
    }
}