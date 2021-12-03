<?php

namespace App\Repositories\Contracts;

interface IEventRepository extends IGenericRepository
{
    public function getImage($file);

    public function publish($id);
}
