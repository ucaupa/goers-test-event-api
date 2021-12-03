<?php

namespace App\Models;

class EventTicket extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'event_schedule_id',
        'name',
        'price',
        'quota',
        'start_sale_date',
        'end_sale_date',
    ];
}
