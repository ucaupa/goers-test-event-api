<?php

namespace App\Transformers;

use App\Http\Responses\OrderResponse;
use App\Models\Order;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'detail',
    ];

    /**
     * Turn this item object into a generic array
     *
     * @param Order $model
     * @return array
     */
    public function transform(Order $model)
    {
        $response = new OrderResponse($model);

        return $response->serialize();
    }

    /**
     * @param Order $model
     * @return Collection|Item|Primitive
     */
    public function includeDetail(Order $model)
    {
        $data = $model->detail;

        return $data ? $this->collection($data, app(TransactionDetailTransformer::class), false) : $this->primitive(null);
    }
}
