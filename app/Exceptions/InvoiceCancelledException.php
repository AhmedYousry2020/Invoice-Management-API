<?php

namespace App\Exceptions;

use Exception;

class InvoiceCancelledException extends Exception
{
    protected $message = 'Cannot record payment on a cancelled invoice.';
}