<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PaymentMethod;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', array_column(PaymentMethod::cases(), 'value'))],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}