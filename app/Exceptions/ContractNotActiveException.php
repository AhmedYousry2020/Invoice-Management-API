<?php

namespace App\Exceptions;

use Exception;

class ContractNotActiveException extends Exception
{
    protected $message = 'Contract is not active.';
}