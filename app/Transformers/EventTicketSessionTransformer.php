<?php

namespace App\Transformers;

use App\Http\Responses\EventTicketSessionResponse;
use App\Models\EventTicketSession;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class EventTicketSessionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'schedule',
    ];

    /**
     * Turn this item object into a generic array
     *
     * @param EventTicketSession $model
     * @return array
     */
    public function transform(EventTicketSession $model)
    {
        $response = new EventTicketSessionResponse($model);

        return $response->serialize();
    }

    /**
     * @param EventTicketSession $model
     * @return Collection|Item|Primitive
     */
    public function includeSchedule(EventTicketSession $model)
    {
        $data = $model->schedule;

        return $data ? $this->item($data, app(EventScheduleTransformer::class), false) : $this->primitive(null);
    }
}
