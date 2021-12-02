<?php

namespace App\Transformers;

use App\Http\Responses\MessageResponse;
use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Message $model)
    {
        $response = new MessageResponse($model);

        return $response->serialize();
    }
}
