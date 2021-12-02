<?php

namespace App\Http\Responses;

class UserTimesResponse
{
    /**
     * @var string
     * */
    public $createdBy;

    /**
     * @var \Datetime
     * */
    public $createdAt;
    /**
     * @var string
     * */
    public $updatedBy;

    /**
     * @var \Datetime
     * */
    public $updatedAt;
}
