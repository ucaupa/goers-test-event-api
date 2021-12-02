<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;

class Controller extends BaseController
{
    protected $statusCode = Response::HTTP_OK;

    /**
     * Create the response for an error.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildJsonResponse($data, $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

    /**
     * Create the response for an error.
     *
     * @param string $message
     * @param mixed $error
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildErrorResponse($message, $status = 500, $error = null, array $headers = [])
    {
        return response()->json(
            [
                'message' => $message,
                'errors' => $error
            ],
            $status,
            $headers
        );
    }

    /**
     * Create the response for an item.
     *
     * @param mixed $item
     * @param TransformerAbstract $transformer
     * @param string $include
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildItemResponse($item, TransformerAbstract $transformer, $include = null, $status = 200, array $headers = [])
    {
        $resource = new Item($item, $transformer);

        return $this->buildResourceResponse($resource, $include, $status, $headers);
    }

    /**
     * Create the response for a resource.
     *
     * @param ResourceAbstract $resource
     * @param string $include
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildResourceResponse(ResourceAbstract $resource, $include = null, $status = 200, array $headers = [])
    {
        $fractal = new Manager;
        $fractal->setSerializer(new DataArraySerializer());

        if (!empty($include))
            $fractal->parseIncludes($include);

        return response()->json(
            $fractal->createData($resource)->toArray(),
            $status,
            $headers
        );
    }

    /**
     * Create the response for a collection.
     *
     * @param LengthAwarePaginator $collection
     * @param TransformerAbstract $transformer
     * @param null $include
     * @param Cursor|null $cursor
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildCollectionResponse($collection, TransformerAbstract $transformer, $include = null, $status = 200, array $headers = [])
    {
        $resource = new Collection($collection, $transformer);
        $resource->setMeta([
            'total' => $collection->total(),
            'limit' => (int)$collection->perPage(),
            'page' => $collection->currentPage(),
            'from' => $collection->firstItem(),
            'to' => $collection->lastItem(),
        ]);

        return $this->buildResourceResponse($resource, $include, $status, $headers);
    }

    /**
     * Create the response for a collection.
     *
     * @param LengthAwarePaginator $collection
     * @param TransformerAbstract $transformer
     * @param null $include
     * @param array $data
     * @param Cursor|null $cursor
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildCollectionWithCustomDataResponse($collection, TransformerAbstract $transformer, $include = null, $data = [], $status = 200, array $headers = [])
    {
        $resource = new Collection($collection, $transformer);
        $resource->setMeta($data);

        return $this->buildResourceResponse($resource, $include, $status, $headers);
    }

    /**
     * Create the response for a collection.
     *
     * @param $collection
     * @param TransformerAbstract $transformer
     * @param null $include
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function buildCollectionNoMetaResponse($collection, TransformerAbstract $transformer, $include = null, $status = 200, array $headers = [])
    {
        $resource = new Collection($collection, $transformer);

        return $this->buildResourceResponse($resource, $include, $status, $headers);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
