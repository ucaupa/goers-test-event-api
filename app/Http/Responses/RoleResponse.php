<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class RoleResponse
{
    /**
     * @var string
     * */
    public $permission;

    /**
     * @var string
     * */
    public $nama;

    /**
     * @var string
     * */
    public $deskripsi;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    public function __construct($model)
    {
        $this->nama = $model->nama;
        $this->permission = $model->role_permission;
        $this->deskripsi = $model->deskripsi;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
