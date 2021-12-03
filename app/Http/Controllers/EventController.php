<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventPatchRequest;
use App\Http\Requests\EventPostRequest;
use App\Repositories\Contracts\IEventRepository;
use App\Transformers\EventTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventController extends Controller
{
    /*
     * @return IEventRepository
     * */
    protected $repo;

    public function __construct(IEventRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Get(
     *     path="/v1/event",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="getEventListMeta",
     *     tags={"Event"},
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
     *     path="/v1/event/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="getEvent",
     *     tags={"Event"},
     *     summary="Get item of event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
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
     * @param $id
     * @return JsonResponse
     */
    public function getById($id)
    {
        try {
            $include = request('include');

            $data = $this->repo->find($id);

            return $this->buildItemResponse($data, new EventTransformer(), $include);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/event",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="storeEvent",
     *     tags={"Event"},
     *     summary="Create new event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Event",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/EventPostRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * @param Request $request
     * @return JsonResponse|Response|ResponseFactory
     */
    public function store(Request $request)
    {
        try {
            $patch = new EventPostRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->create($model);

            return response($query, Response::HTTP_CREATED);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Patch(
     *     path="/v1/event/{id}",
     *     deprecated=true,
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="updateEvent",
     *     tags={"Event"},
     *     summary="Update event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Event",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/EventPatchRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse|Response|ResponseFactory
     */
    public function update(Request $request, $id)
    {
        try {
            $patch = new EventPatchRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->update($id, $model);

            return response($query, Response::HTTP_ACCEPTED);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/event/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="deleteEvent",
     *     tags={"Event"},
     *     summary="Delete event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * @param $id
     * @return JsonResponse|Response|ResponseFactory
     */
    public function destroy($id)
    {
        try {
            $query = $this->repo->delete($id);

            return response($query, Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function file($file, $extension = '')
    {
        try {
            return $this->repo->getImage($file);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("Not found", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Patch(
     *     path="/v1/event/{id}/publish",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="publishEvent",
     *     tags={"Event"},
     *     summary="Publish event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * @param $id
     * @return JsonResponse|Response|ResponseFactory
     */
    public function publish($id)
    {
        try {
            $query = $this->repo->publish($id);

            return response($query, Response::HTTP_ACCEPTED);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
