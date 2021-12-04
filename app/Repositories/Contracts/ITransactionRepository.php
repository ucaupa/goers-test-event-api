<?php

namespace App\Repositories\Contracts;

interface ITransactionRepository
{
    public function get($transactionId);

    public function checkout($model);

    public function pay($model);

    public function callback($model);
}
