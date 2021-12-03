<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IPublicEventRepository;
use App\Transformers\EventTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PublicEventController extends Controller
{
    /*
     * @return IPublicEventRepository
     * */
    protected $repo;

    public function __construct(IPublicEventRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Get(
     *     path="/v2/event",
     *     operationId="getEventListMeta",
     *     tags={"Public - Event"},
     *     summary="Get list of event with pagination",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(ref="#/components/parameters/Pagination.Page"),
     *     @OA\Parameter(ref="#/components/parameters/Pagination.Limit"),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordered column",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"name"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Includes relationship",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"schedules,images,tickets.sessions.schedule", "schedules", "images", "tickets.sessions.schedule"}
     *         )
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Sorting"),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * */
    public function get()
    {
        try {
            $page = request('page', 1);
            $limit = request('limit');
            $order = request('order');
            $sort = request('sort', 'desc');
            $filter = request('filter');
            $include = request('include');

            if ($limit) {
                $result = $this->repo->get($page, $limit, $order, $sort, $filter);
            } else {
                $result = $this->repo->getAll($order, $sort, $filter);
            }

            if ($result) {
                if ($limit) {
                    return $this->buildCollectionResponse($result, new EventTransformer(), $include);
                } else {
                    return $this->buildCollectionNoMetaResponse($result, new EventTransformer(), $include);
                }
            } else {
                return response(null, Response::HTTP_NO_CONTENT);
            }
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/event/{slugId}",
     *     operationId="getEvent",
     *     tags={"Public - Event"},
     *     summary="Get item of event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="slugId",
     *         in="path",
     *         description="Event Slug ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Includes relationship",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"schedules,images,tickets.sessions.schedule", "schedules", "images", "tickets.sessions.schedule"}
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     * @param $slugId
     * @return JsonResponse
     */
    public function getById($slugId)
    {
        try {
            $include = request('include');

            $data = $this->repo->findBySlug($slugId);

            return $this->buildItemResponse($data, new EventTransformer(), $include);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("Not Found", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
