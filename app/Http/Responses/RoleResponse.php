<?php

namespace App\Http\Responses;

class RoleResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $description;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->description = $model->description;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
