<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="UserAuthPatchRequest",
 *     required={"username","name","password","password_confirmation","role"}
 * )
 */
class UserAuthPatchRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $name;

    /**
     * @OA\Property()
     * @var bool
     * */
    public $isActive;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            'name' => 'required|string',
            'isActive' => 'required|boolean',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->name = property_exists($object, 'name') ? $object->name : null;
        $this->isActive = property_exists($object, 'isActive') ? $object->isActive ? 1 : 0 : null;
    }

    public function parse()
    {
        $result = array(
            'name' => $this->name,
            'is_active' => $this->isActive,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
