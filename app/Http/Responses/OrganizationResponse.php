<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use App\Http\Responses\JenisOrganisasiResponse;

class OrganizationResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $name;

    /**
     * @var string
     * */
    public $description;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->name = $model->name;
        $this->description = $model->description;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
