<?php

namespace App\Repositories\Contracts;

interface IPublicEventRepository extends IGenericRepository
{
    public function findBySlug($slugId);
}
