<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(schema="LoginRequest", required={"username", "password"})
 */
class LoginRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $username;

    /**
     * @OA\Property()
     * @var string
     * */
    public $password;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "username" => "required|string",
            "password" => "required|string",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->username = property_exists($object, 'username') ? $object->username : null;
        $this->password = property_exists($object, 'password') ? $object->password : null;
    }

    public function parse()
    {
        $result = array(
            'username' => $this->username,
            'password' => $this->password,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
