<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class JenisOrganisasiResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $nama;

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
        $this->id = $model->id;
        $this->nama = $model->nama;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
