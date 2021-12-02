<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\IUserRepository;

class UserRepository extends GenericRepository implements IUserRepository
{
    public function __construct()
    {
        parent::__construct(app(User::class));
    }
}
