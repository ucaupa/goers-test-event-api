<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class UserResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $username;

    /**
     * @var string
     * */
    public $email;

    /**
     * @var string
     * */
    public $name;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->name = $model->nama;
        $this->email = $model->email;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
