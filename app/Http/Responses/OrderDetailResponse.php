<?php

namespace App\Http\Responses;

class OrderDetailResponse
{
    /**
     * @var int
     * */
    public $orderId;

    /**
     * @var int
     * */
    public $ticketId;

    /**
     * @var int
     * */
    public $qty;

    /**
     * @var int
     * */
    public $price;

    public function __construct($model)
    {
        $this->orderId = $model->order_id;
        $this->ticketId = $model->event_ticket_id;
        $this->qty = $model->qty;
        $this->price = $model->price;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
