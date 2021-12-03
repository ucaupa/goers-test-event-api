<?php

namespace App\Http\Responses;

class EventTicketResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var int
     * */
    public $eventId;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->eventId = $model->name;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
