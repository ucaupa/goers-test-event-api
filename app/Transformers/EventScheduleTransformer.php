<?php

namespace App\Transformers;

use App\Http\Responses\EventScheduleResponse;
use App\Models\EventSchedule;
use League\Fractal\TransformerAbstract;

class EventScheduleTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @param EventSchedule $model
     * @return array
     */
    public function transform(EventSchedule $model)
    {
        $response = new EventScheduleResponse($model);

        return $response->serialize();
    }
}
