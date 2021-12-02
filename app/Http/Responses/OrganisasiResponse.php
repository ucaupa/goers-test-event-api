<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use App\Http\Responses\JenisOrganisasiResponse;

class OrganisasiResponse
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
     * @var JenisOrganisasiResponse
     * */
    public $jenis_organisasi;

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
        $this->jenis_organisasi = $model->jenis ? new JenisOrganisasiResponse($model->jenis) : null;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
