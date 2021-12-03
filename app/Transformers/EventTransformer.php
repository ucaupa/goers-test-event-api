<?php

namespace App\Transformers;

use App\Http\Responses\EventResponse;
use App\Models\Event;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'schedules',
        'images',
        'tickets',
    ];

    /**
     * Turn this item object into a generic array
     *
     * @param Event $model
     * @return array
     */
    public function transform(Event $model)
    {
        $response = new EventResponse($model);

        return $response->serialize();
    }

    /**
     * @param Event $model
     * @return Collection|Item|Primitive
     */
    public function includeSchedules(Event $model)
    {
        $data = $model->schedules;

        return $data ? $this->collection($data, app(EventScheduleTransformer::class), false) : $this->primitive(null);
    }

    /**
     * @param Event $model
     * @return Collection|Item|Primitive
     */
    public function includeImages(Event $model)
    {
        $data = $model->images;

        return $data ? $this->collection($data, app(EventImageTransformer::class), false) : $this->primitive(null);
    }

    /**
     * @param Event $model
     * @return Collection|Item|Primitive
     */
    public function includeTickets(Event $model)
    {
        $data = $model->tickets;

        return $data ? $this->collection($data, app(EventTicketTransformer::class), false) : $this->primitive(null);
    }
}
