<?php
namespace App\Services\Taxes;

interface TaxCalculatorInterface
{
    public function calculate(float $amount): float;
}