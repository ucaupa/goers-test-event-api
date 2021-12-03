<?php

namespace App\Transformers;

use App\Http\Responses\EventTicketResponse;
use App\Models\EventTicket;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class EventTicketTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'sessions',
    ];

    /**
     * Turn this item object into a generic array
     *
     * @param EventTicket $model
     * @return array
     */
    public function transform(EventTicket $model)
    {
        $response = new EventTicketResponse($model);

        return $response->serialize();
    }

    /**
     * @param EventTicket $model
     * @return Collection|Item|Primitive
     */
    public function includeSessions(EventTicket $model)
    {
        $data = $model->sessions;

        return $data ? $this->collection($data, app(EventTicketSessionTransformer::class), false) : $this->primitive(null);
    }
}
