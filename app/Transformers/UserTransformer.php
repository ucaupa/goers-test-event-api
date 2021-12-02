<?php

namespace App\Transformers;

use App\Http\Responses\UserResponse;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
     * @param User $model
     * @return array
     */
    public function transform(User $model)
    {
        $response = new UserResponse($model);

        return $response->serialize();
    }
}
