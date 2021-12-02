<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class UserAuthResponse
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
    public $groupKey;

    /**
     * @var string
     * */
    public $organisasiId;

    /**
     * @var string
     * */
    public $jabatanId;

    /**
     * @var string
     * */
    public $email;

    /**
     * @var string
     * */
    public $nama;

    /**
     * @var int
     * */
    public $nik;

    /**
     * @var int
     * */
    public $nip;

    /**
     * @var string
     * */
    public $telepon;

    /**
     * @var OrganisasiResponse
     * */
    public $organisasi;

    /**
     * @var JabatanResponse
     * */
    public $jabatan;

    /**
     * @var RoleResponse
     * */
    public $role;

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
        $this->username = $model->username;
        $this->nama = $model->nama;
        $this->email = $model->email;
        $this->nik = (int)$model->nik;
        $this->nip = (int)$model->nip;
        $this->telepon = $model->telepon;
        $this->organisasiId = $model->organisasi_id;
        $this->jabatanId = $model->jabatan_id;
        $this->organisasi = $model->organisasi ? new OrganisasiResponse($model->organisasi) : null;
        $this->jabatan = $model->jabatan ? new JabatanResponse($model->jabatan) : null;
        $this->role = $model->role ? new RoleResponse($model->role) : null;
        $this->groupKey = $model->group_key;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
