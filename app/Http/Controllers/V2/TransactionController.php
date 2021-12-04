<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\PayRequest;
use App\Repositories\Contracts\ITransactionRepository;
use App\Transformers\TransactionTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransactionController extends Controller
{
    /*
     * @return ITransactionRepository
     * */
    protected $repo;

    public function __construct(ITransactionRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Get(
     *     path="/v2/transaction/{id}",
     *     operationId="getTransaction",
     *     tags={"Public - Transaction"},
     *     summary="Get item of transaction",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
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
    public function get($id)
    {
        try {
            $data = $this->repo->get($id);

            return $this->buildItemResponse($data, new TransactionTransformer(), 'detail');
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("Not Found", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/transaction/checkout",
     *     operationId="checkoutTicket",
     *     tags={"Public - Transaction"},
     *     summary="Checkout tickets",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Event",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/CheckoutRequest")
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
    public function checkout(Request $request)
    {
        try {
            $patch = new CheckoutRequest($request->all());
            $model = $patch->parse();

            $result = $this->repo->checkout($model);

            return $this->buildJsonResponse([
                'message' => 'Success',
                'data' => $result,
                'success' => true,
            ]);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/transaction/pay",
     *     operationId="payOrder",
     *     tags={"Public - Transaction"},
     *     summary="Pay order",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Event",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PayRequest")
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
    public function pay(Request $request)
    {
        try {
            $patch = new PayRequest($request->all());
            $model = $patch->parse();

            $result = $this->repo->pay($model);

            return $this->buildJsonResponse([
                'message' => $result,
                'data' => null,
                'success' => true
            ]);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("Not found", Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/transaction/callback",
     *     operationId="callbackOrder",
     *     tags={"Public - Transaction"},
     *     summary="Callback notification order from midtrans",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
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
    public function callback(Request $request)
    {
        try {
            $query = $this->repo->callback($request->toArray());

            return response($query, Response::HTTP_OK);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (HttpException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
