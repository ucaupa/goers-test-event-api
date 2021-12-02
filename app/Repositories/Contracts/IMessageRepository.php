<?php

namespace App\Repositories\Contracts;

interface IMessageRepository extends IGenericRepository
{
    public function getAllMessage($order, $sort, $filter = null, $status = null);

    public function read($key);
}
