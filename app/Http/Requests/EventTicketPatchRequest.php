<?php

namespace App\Http\Requests;

use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="EventTicketPatchRequest",
 *     required={"name", "quota", "startSale", "endSale", "session"}
 * )
 */
class EventTicketPatchRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $name;

    /**
     * @OA\Property()
     * @var int
     * */
    public $price;

    /**
     * @OA\Property()
     * @var int
     * */
    public $quota;

    /**
     * @OA\Property()
     * @var DateTime
     * */
    public $startSale;

    /**
     * @OA\Property()
     * @var DateTime
     * */
    public $endSale;

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          type="string"
     *     ),
     * )
     * */
    public $session;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "name" => "required",
            "price" => "nullable|integer",
            "quota" => "required|integer|min:10",
            "startSale" => "required|date",
            "endSale" => "required|date|after:startSale",
            "session" => "required|array",
            "session.*" => "required",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->name = property_exists($object, 'name') ? $object->name : null;
        $this->price = property_exists($object, 'price') ? $object->price : null;
        $this->quota = property_exists($object, 'quota') ? $object->quota : null;
        $this->startSale = property_exists($object, 'startSale') ? $object->startSale : null;
        $this->endSale = property_exists($object, 'endSale') ? $object->endSale : null;
        $this->session = property_exists($object, 'session') ? $object->session : null;
    }

    public function parse()
    {
        $result = array(
            'name' => $this->name,
            'price' => $this->price,
            'quota' => $this->quota,
            'start_sale_date' => $this->startSale,
            'end_sale_date' => $this->endSale,
            'session' => $this->session,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
