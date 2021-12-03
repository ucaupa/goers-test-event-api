<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use DateTime;

class EventScheduleResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var int
     * */
    public $eventId;

    /**
     * @var DateTime
     * */
    public $startDate;

    /**
     * @var DateTime
     * */
    public $endDate;

    /**
     * @var DateTime
     * */
    public $startTime;

    /**
     * @var DateTime
     * */
    public $endTime;

    /**
     * @var DateTime
     * */
    public $startDatetime;

    /**
     * @var DateTime
     * */
    public $endDatetime;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->eventId = $model->event_id;
        $this->startDate = $model->start_date ? Carbon::parse($model->start_date)->format('d-m-Y') : null;
        $this->endDate = $model->end_date ? Carbon::parse($model->end_date)->format('d-m-Y') : null;
        $this->startTime = $model->start_date ? Carbon::parse($model->start_date)->format('H:i') : null;
        $this->endTime = $model->end_date ? Carbon::parse($model->end_date)->format('H:i') : null;
        $this->startDatetime = $model->start_date ? Carbon::parse($model->start_date)->format('d-m-Y H:i') : null;
        $this->endDatetime = $model->end_date ? Carbon::parse($model->end_date)->format('d-m-Y H:i') : null;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
