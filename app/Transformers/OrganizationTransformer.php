<?php

namespace App\Transformers;

use App\Http\Responses\OrganizationResponse;
use App\Models\Organization;
use League\Fractal\TransformerAbstract;

class OrganizationTransformer extends TransformerAbstract
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
     * @param Organization $model
     * @return array
     */
    public function transform(Organization $model)
    {
        $response = new OrganizationResponse($model);

        return $response->serialize();
    }
}
