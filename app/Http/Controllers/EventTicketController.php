<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventTicketPatchRequest;
use App\Http\Requests\EventTicketPostRequest;
use App\Repositories\Contracts\IEventRepository;
use App\Repositories\Contracts\IEventTicketRepository;
use App\Transformers\EventTicketTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventTicketController extends Controller
{
    /*
     * @return IEventTicketRepository
     * */
    protected $repo;

    /*
     * @return IEventRepository
     * */
    protected $repoEvent;

    public function __construct(IEventTicketRepository $repo, IEventRepository $repoEvent)
    {
        $this->repo = $repo;
        $this->repoEvent = $repoEvent;
    }

    /**
     * @OA\Get(
     *     path="/v1/event/{eventId}/ticket",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="getEventTicketListMeta",
     *     tags={"Ticket Event"},
     *     summary="Get list of event with pagination",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
     *             enum={"sessions.schedule"}
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
     * @param $eventId
     * @return JsonResponse|Response|ResponseFactory
     */
    public function get($eventId)
    {
        try {
            $this->repoEvent->find($eventId);

            $page = request('page', 1);
            $limit = request('limit');
            $order = request('order');
            $sort = request('sort', 'desc');
            $filter = request('filter');
            $include = request('include');

            if ($limit) {
                $result = $this->repo->get($eventId, $page, $limit, $order, $sort, $filter);
            } else {
                $result = $this->repo->getAll($eventId, $order, $sort, $filter);
            }

            if ($result) {
                if ($limit) {
                    return $this->buildCollectionResponse($result, new EventTicketTransformer(), $include);
                } else {
                    return $this->buildCollectionNoMetaResponse($result, new EventTicketTransformer(), $include);
                }
            } else {
                return response(null, Response::HTTP_NO_CONTENT);
            }
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("Event Not Found", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/event/{eventId}/ticket/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="getEventTicket",
     *     tags={"Ticket Event"},
     *     summary="Get item of event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event Ticket ID",
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
     *             enum={"sessions.schedule"}
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     * @param $eventId
     * @param $id
     * @return JsonResponse
     */
    public function getById($eventId, $id)
    {
        try {
            $this->repoEvent->find($eventId);

            $include = request('include');

            $data = $this->repo->find($eventId, $id);

            return $this->buildItemResponse($data, new EventTicketTransformer(), $include);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/event/{eventId}/ticket",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="storeEventTicket",
     *     tags={"Ticket Event"},
     *     summary="Create new event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="EventTicket",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/EventTicketPostRequest")
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
     * @param $eventId
     * @return JsonResponse|Response|ResponseFactory
     */
    public function store(Request $request, $eventId)
    {
        try {
            $this->repoEvent->find($eventId);

            $patch = new EventTicketPostRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->create($eventId, $model);

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
     *     path="/v1/event/{eventId}/ticket/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="updateEventTicket",
     *     tags={"Ticket Event"},
     *     summary="Update event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event Ticket ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="EventTicket",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/EventTicketPatchRequest")
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
     * @param $eventId
     * @param $id
     * @return JsonResponse|Response|ResponseFactory
     */
    public function update(Request $request, $eventId, $id)
    {
        try {
            $this->repoEvent->find($eventId);

            $patch = new EventTicketPatchRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->update($eventId, $id, $model);

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
     *     path="/v1/event/{eventId}/ticket/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="deleteEventTicket",
     *     tags={"Ticket Event"},
     *     summary="Delete event",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event Ticket ID",
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
     * @param $eventId
     * @param $id
     * @return JsonResponse|Response|ResponseFactory
     */
    public function destroy($eventId, $id)
    {
        try {
            $this->repoEvent->find($eventId);

            $query = $this->repo->delete($eventId, $id);

            return response($query, Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
