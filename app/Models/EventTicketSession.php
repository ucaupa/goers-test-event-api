<?php

namespace App\Models;

class EventTicketSession extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_ticket_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_ticket_id',
        'event_schedule_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(EventSchedule::class, 'event_schedule_id', 'id');
    }
}
