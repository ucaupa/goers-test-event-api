<?php

namespace App\Transformers;

use App\Http\Responses\UserAuthResponse;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserAuthTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'detail'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(User $model)
    {
        $response = new UserAuthResponse($model);

        return $response->serialize();
    }
}
