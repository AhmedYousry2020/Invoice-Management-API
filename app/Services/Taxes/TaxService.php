<?php
namespace App\Services\Taxes;

class TaxService
{
    public function __construct(private array $calculators = [])
    {
    }

    public function calculateTotal(float $amount): float
    {
        return array_reduce($this->calculators, function ($carry, TaxCalculatorInterface $calculator) use ($amount) {
            return $carry + $calculator->calculate($amount);
        }, 0);
    }
}