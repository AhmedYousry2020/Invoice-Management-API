<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\DTOs\CreateInvoiceDTO;
use App\DTOs\CreatePaymentDTO;
use App\Http\Requests\StorePaymentRequest;
use App\Services\InvoiceService;
use App\Http\Resources\InvoiceResource;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function store(StoreInvoiceRequest $request, Contract $contract)
    {
        $this->authorize('create', [Invoice::class, $contract]);

        $dto = CreateInvoiceDTO::fromRequest($request);
        $invoice = $this->invoiceService->createInvoice($dto);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'data'    => new InvoiceResource($invoice)
        ], 201);
    }

    public function index(Request $request, Contract $contract)
    {
        $this->authorize('view', [Invoice::class, $contract]);

        $filters = $request->only(['status', 'from', 'to']);
        $perPage = $request->input('per_page', 10);

        $invoices = $this->invoiceService->listInvoices(
            $contract->id,
            $filters,
            $perPage
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data'    => InvoiceResource::collection($invoices),
            'meta'    => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'per_page'     => $invoices->perPage(),
                'total'        => $invoices->total(),
            ]
        ]);
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', [Invoice::class, $invoice->contract]);

        $invoice = $this->invoiceService
            ->getInvoiceDetails($invoice->id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice details retrieved successfully',
            'data'    => new InvoiceResource($invoice)
        ]);
    }

    public function recordPayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $this->authorize('recordPayment', [Invoice::class, $invoice]);

        $dto = CreatePaymentDTO::fromRequest($request);
        $payment = $this->invoiceService->recordPayment($dto);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data'    => $payment
        ], 201);
    }
    public function summary(Contract $contract)
    {
        $summary = $this->invoiceService->getContractSummary($contract->id);

        return response()->json([
            'success' => true,
            'message' => 'Contract summary retrieved successfully',
            'data'    => $summary
        ]);
    }

}