<?php

namespace App\Http\Requests;

use DateTime;

/**
 * @OA\Schema(
 *     schema="EventSchedulePostRequest"
 * )
 */
class EventSchedulePostRequest
{
    /**
     * @OA\Property()
     * @var DateTime
     * */
    public $startDate;

    /**
     * @OA\Property()
     * @var DateTime
     * */
    public $endDate;
}
