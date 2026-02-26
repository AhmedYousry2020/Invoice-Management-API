<?php
namespace App\Services\Taxes;

class VATCalculator implements TaxCalculatorInterface
{
    public function calculate(float $amount): float
    {
        return $amount * 0.15;
    }
}