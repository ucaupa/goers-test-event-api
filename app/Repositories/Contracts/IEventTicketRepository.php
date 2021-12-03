<?php

namespace App\Repositories\Contracts;

interface IEventTicketRepository
{
    public function get($eventId, $page, $limit, $order, $sort, $filter = null);

    public function getAll($eventId, $order, $sort, $filter = null);

    public function find($eventId, $id);

    public function create($eventId, $model);

    public function update($eventId, $id, $model);

    public function delete($eventId, $id);
}
