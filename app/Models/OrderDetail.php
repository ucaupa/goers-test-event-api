<?php

namespace App\Models;

class OrderDetail extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'event_ticket_id',
        'qty',
        'price',
    ];

    public function head()
    {
        return $this->hasOne(Order::class, 'order_id', 'id');
    }

    public function ticket()
    {
        return $this->hasOne(EventTicket::class, 'id', 'event_ticket_id');
    }
}
