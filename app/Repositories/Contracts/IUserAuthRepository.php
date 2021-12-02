<?php

namespace App\Repositories\Contracts;

interface IUserAuthRepository extends IGenericRepository
{
    public function authentication($model);

    public function changePassword($model);
}
