<?php
namespace App\Repositories\Contracts;

use App\Models\Contract;

interface ContractRepositoryInterface
{
    public function findById(int $id): ?Contract;
}