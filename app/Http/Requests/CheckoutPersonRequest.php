<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="CheckoutPersonRequest"
 * )
 */
class CheckoutPersonRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $firstName;

    /**
     * @OA\Property()
     * @var string
     * */
    public $lastName;

    /**
     * @OA\Property()
     * @var string
     * */
    public $email;

    /**
     * @OA\Property()
     * @var string
     * */
    public $phone;

    /**
     * @OA\Property()
     * @var string
     * */
    public $gender;
}
