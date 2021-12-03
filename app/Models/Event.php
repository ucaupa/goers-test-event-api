<?php

namespace App\Models;

class Event extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'category_id',
        'location',
        'is_draft',
    ];

    public function schedules()
    {
        return $this->hasMany(EventSchedule::class, 'event_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(EventImage::class, 'event_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(EventTicket::class, 'event_id', 'id');
    }
}
