<?php

namespace App\Http\Responses;

class EventTicketSessionResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var int
     * */
    public $ticketId;

    /**
     * @var int
     * */
    public $scheduleId;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->ticketId = $model->event_ticket_id;
        $this->scheduleId = $model->event_schedule_id;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
