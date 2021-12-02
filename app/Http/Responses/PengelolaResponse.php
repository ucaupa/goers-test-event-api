<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use App\Http\Responses\UserTimesResponse;
use App\Models\User;

class PengelolaResponse extends UserTimesResponse
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
    public $group_key;

    /**
     * @var int
     * */
    public $organisasi_id;

    /**
     * @var int
     * */
    public $jabatan_id;

    /**
     * @var int
     * */
    public $tipe_pengguna_id;

    /**
     * @var int
     * */
    public $group_jabatan_id;

    /**
     * @var int
     * */
    public $role_id;

    /**
     * @var string
     * */
    public $nama;

    /**
     * @var string
     * */
    public $email;

    /**
     * @var string
     * */
    public $nik;

    /**
     * @var string
     * */
    public $nip;

    /**
     * @var string
     * */
    public $telepon;

    /**
     * @var string
     * */
    public $foto;

    /**
     * @var string
     * */
    public $status_text;

    /**
     * @var string
     * */
    public $jabatan;

    /**
     * @var bool
     * */
    public $status;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->group_key = $model->group_key;
        $this->organisasi_id = $model->organisasi_id;
        $this->jabatan_id = $model->jabatan_id;
        $this->tipe_pengguna_id = $model->tipe_pengguna_id;
        $this->group_jabatan_id = $model->group_jabatan_id;
        $this->role_id = $model->role_id;
        $this->nama = $model->nama;
        $this->email = $model->email;
        $this->nik = $model->nik;
        $this->nip = $model->nip;
        $this->telepon = $model->telepon;
        $this->foto = $model->foto === null ? null : config('app.url') . '/pengelola' . '/' . $model->foto;
        $this->status_text = $model->status == User::ACTIVE ? 'Aktif' : 'Tidak Aktif';
        $this->status = $model->status;
        $this->jabatan = $model->jabatan->nama;
        $this->createdBy = $model->created_by;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedBy = $model->updated_by;
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
