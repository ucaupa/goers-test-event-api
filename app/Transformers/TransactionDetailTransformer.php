<?php

namespace App\Transformers;

use App\Http\Responses\OrderDetailResponse;
use App\Models\OrderDetail;
use League\Fractal\TransformerAbstract;

class TransactionDetailTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * Turn this item object into a generic array
     *
     * @param OrderDetail $model
     * @return array
     */
    public function transform(OrderDetail $model)
    {
        $response = new OrderDetailResponse($model);

        return $response->serialize();
    }
}
