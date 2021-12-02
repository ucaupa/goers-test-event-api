<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="OrganizationPostRequest",
 *     required={"username", "password"}
 * )
 */
class OrganizationPostRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $name;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "name" => [
                "required",
                Rule::unique('organizations')->where(function ($query) use ($payload) {
                    return $query->where('slug', Str::slug($payload['name'], ''));
                })

            ],
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->name = property_exists($object, 'name') ? $object->name : null;
    }

    public function parse()
    {
        $result = array(
            'name' => $this->name,
            'slug' => Str::slug($this->name, ''),
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
