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
 *     schema="CheckoutRequest"
 * )
 */
class CheckoutRequest
{
    /**
     * @OA\Property()
     * @var int
     * */
    public $eventId;

    /**
     * @OA\Property()
     * @var CheckoutPersonRequest
     * */
    public $person;

    /**
     * @OA\Property()
     * @var CheckoutTicketRequest[]
     * */
    public $tickets;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "eventId" => [
                "required",
                "integer",
                Rule::exists('events', 'id')->where('is_draft', 0)
            ],
            "person" => "required",
            "person.firstName" => "required",
            "person.lastName" => "required",
            "person.email" => "required|email",
            "person.phone" => "required",
            "person.gender" => "required|in:L,P",
            "tickets" => "required|array",
            "tickets.*.ticketId" => [
                "required",
                "integer",
                Rule::exists('event_tickets', 'id')->where('event_id', $payload['eventId'])
            ],
            "tickets.*.qty" => "required|integer|min:1|max:10",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->eventId = property_exists($object, 'eventId') ? $object->eventId : null;
        $this->person = property_exists($object, 'person') ? $object->person : null;
        $this->tickets = property_exists($object, 'tickets') ? $object->tickets : null;
    }

    public function parse()
    {
        $now = Carbon::now();
        $format = 'TRX/' . $this->eventId . $now->format('siH-dmY') . '-INV';

        $result = array(
            'event_id' => $this->eventId,
            'invoice' => $format,
            'order_id' => Str::uuid()->toString(),
            'first_name' => $this->person['firstName'],
            'last_name' => $this->person['lastName'],
            'email' => $this->person['email'],
            'phone_number' => $this->person['phone'],
            'gender' => $this->person['gender'],
            'tickets' => $this->tickets,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
