<?php

namespace App\Transformers;

use App\Http\Responses\EventImageResponse;
use App\Models\EventImage;
use League\Fractal\TransformerAbstract;

class EventImageTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @param EventImage $model
     * @return array
     */
    public function transform(EventImage $model)
    {
        $response = new EventImageResponse($model);

        return $response->serialize();
    }
}
