<?php

namespace App\Transformers;

use App\Http\Responses\PengelolaResponse;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class PengelolaTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(User $model)
    {
        $response = new PengelolaResponse($model);

        return $response->serialize();
    }
}
