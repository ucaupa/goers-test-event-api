<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="UserRegistrationPostRequest",
 *     required={"username", "password"}
 * )
 */
class UserRegistrationPostRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $name;

    /**
     * @OA\Property()
     * @var string
     * */
    public $username;

    /**
     * @OA\Property()
     * @var string
     * */
    public $email;

    /**
     * @OA\Property()
     * @var string
     * */
    public $phoneNumber;

    /**
     * @OA\Property()
     * @var string
     * */
    public $password;

    /**
     * @OA\Property()
     * @var string
     * */
    public $password_confirmation;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "name" => "required",
            "username" => "required|min:6|unique:users,username",
            "email" => "required|email|unique:users,email",
            "phoneNumber" => "required",
            "password" => "required|min:8|confirmed",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->name = property_exists($object, 'name') ? $object->name : null;
        $this->username = property_exists($object, 'username') ? $object->username : null;
        $this->email = property_exists($object, 'email') ? $object->email : null;
        $this->phoneNumber = property_exists($object, 'phoneNumber') ? $object->phoneNumber : null;
        $this->password = property_exists($object, 'password') ? $object->password : null;
    }

    public function parse()
    {
        $result = array(
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'password' => Hash::make($this->password),
            'role_id' => 'user',
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
