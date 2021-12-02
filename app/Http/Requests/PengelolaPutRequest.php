<?php

namespace App\Http\Requests;

use App\Helpers\Upload;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="PengelolaPutRequest",
 * )
 */
class PengelolaPutRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $username;

    /**
     * @OA\Property()
     * @var int
     * */
    public $organisasi_id;

    /**
     * @OA\Property()
     * @var int
     * */
    public $jabatan_id;

    /**
     * @OA\Property()
     * @var int
     * */
    public $tipe_pengguna_id;

    /**
     * @OA\Property()
     * @var int
     * */
    public $group_jabatan_id;

    /**
     * @OA\Property()
     * @var int
     * */
    public $role_id;

    /**
     * @OA\Property()
     * @var string
     * */
    public $nama;

    /**
     * @OA\Property()
     * @var string
     * */
    public $email;

    /**
     * @OA\Property()
     * @var string
     * */
    public $nik;

    /**
     * @OA\Property()
     * @var string
     * */
    public $nip;

    /**
     * @OA\Property()
     * @var string
     * */
    public $telepon;

    /**
     * @OA\Property(
     *     type="string",
     *     format="binary",
     *     description="{.jpg, .jpeg, .png} max: 512kb",
     * )
     * */
    public $foto;

    public function __construct($payload, $id)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            'username' => 'required|unique:users,username,' . $id . ',id,deleted_at,NULL',
            'organisasi_id' => 'required',
            'jabatan_id' => 'required',
            'tipe_pengguna_id' => 'required',
            'group_jabatan_id' => 'required',
            'role_id' => 'required',
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'nik' => 'required|max:16',
            'nip' => 'required|max:18',
            'telepon' => 'required',
            'foto' => 'nullable|mimes:png,jpg',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->username = property_exists($object, 'username') ? $object->username : null;
        $this->group_key = property_exists($object, 'group_key') ? $object->group_key : null;
        $this->organisasi_id = property_exists($object, 'organisasi_id') ? $object->organisasi_id : null;
        $this->jabatan_id = property_exists($object, 'jabatan_id') ? $object->jabatan_id : null;
        $this->tipe_pengguna_id = property_exists($object, 'tipe_pengguna_id') ? $object->tipe_pengguna_id : null;
        $this->group_jabatan_id = property_exists($object, 'group_jabatan_id') ? $object->group_jabatan_id : null;
        $this->role_id = property_exists($object, 'role_id') ? $object->role_id : null;
        $this->nama = property_exists($object, 'nama') ? $object->nama : null;
        $this->email = property_exists($object, 'email') ? $object->email : null;
        $this->nik = property_exists($object, 'nik') ? $object->nik : null;
        $this->nip = property_exists($object, 'nip') ? $object->nip : null;
        $this->telepon = property_exists($object, 'telepon') ? $object->telepon : null;
        $this->foto = property_exists($object, 'foto') ? $object->foto : null;
    }

    public function parse()
    {
        $filename = null;

        if (is_file($this->foto)) {
            $filename = Upload::store($this->foto, 'pengelola', $this->username);
        }

        $result = array(
            'username' => $this->username,
            'group_key' => auth()->user()->group_key,
            'organisasi_id' => $this->organisasi_id,
            'jabatan_id' => $this->jabatan_id,
            'tipe_pengguna_id' => $this->tipe_pengguna_id,
            'group_jabatan_id' => $this->group_jabatan_id,
            'role_id' => $this->role_id,
            'nama' => $this->nama,
            'email' => $this->email,
            'nik' => $this->nik,
            'nip' => $this->nip,
            'telepon' => $this->telepon,
            'foto' => $filename,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
