<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="PayRequest"
 * )
 */
class PayRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $orderId;

    /**
     * @OA\Property()
     * @var int
     * */
    public $paymentMethodId;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "orderId" => [
                "required",
                "uuid",
                Rule::exists('orders', 'order_id')
            ],
            "paymentMethodId" => "required|exists:payment_method,id",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->orderId = property_exists($object, 'orderId') ? $object->orderId : null;
        $this->paymentMethodId = property_exists($object, 'paymentMethodId') ? $object->paymentMethodId : null;
    }

    public function parse()
    {
        $result = array(
            'order_id' => $this->orderId,
            'payment_method' => $this->paymentMethodId,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
