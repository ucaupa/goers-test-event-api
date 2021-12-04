<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="CheckoutTicketRequest"
 * )
 */
class CheckoutTicketRequest
{
    /**
     * @OA\Property()
     * @var int
     * */
    public $ticketId;

    /**
     * @OA\Property()
     * @var int
     * */
    public $qty;
}
