<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="AuthChangePasswordPatchRequest",
 *     required={"password","password_confirmation"}
 * )
 */
class AuthChangePasswordPatchRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $oldPassword;

    /**
     * @OA\Property()
     * @var string
     * */
    public $newPassword;

    /**
     * @OA\Property()
     * @var string
     * */
    public $newPassword_confirmation;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->oldPassword = property_exists($object, 'oldPassword') ? $object->oldPassword : null;
        $this->newPassword = property_exists($object, 'newPassword') ? $object->newPassword : null;
    }

    public function parse()
    {
        $result = array(
            'old_password' => $this->oldPassword,
            'new_password' => $this->newPassword,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
