<?php

namespace App\Repositories\Contracts;

interface IGenericRepository
{
    public function get($page, $limit, $order, $sort, $filter = null);

    public function getAll($order, $sort, $filter = null);

    public function find($id);

    public function create($model);

    public function update($id, $model);

    public function delete($id);
}
