<?php

namespace App\Transformers;

use App\Http\Responses\EventTicketResponse;
use App\Models\EventTicket;
use League\Fractal\TransformerAbstract;

class EventTicketTransformer extends TransformerAbstract
{
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
}
