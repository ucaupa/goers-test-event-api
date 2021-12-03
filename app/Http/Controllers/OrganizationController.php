<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationPostRequest;
use App\Repositories\Contracts\IOrganizationRepository;
use App\Transformers\OrganizationTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrganizationController extends Controller
{
    /*
     * @return IOrganizationRepository
     * */
    protected $repo;

    public function __construct(IOrganizationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Get(
     *     path="/v1/organization",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="getOrganization",
     *     tags={"Organization"},
     *     summary="Get item of organization",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     */
    public function getById()
    {
        try {
            $id = Auth::user()->organization_id;
            $include = request('include');

            $data = $this->repo->find($id);

            return $this->buildItemResponse($data, new OrganizationTransformer(), $include);
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("No query results for " . $id, Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/organization",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="storeOrganization",
     *     tags={"Organization"},
     *     summary="Create new organization",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Organization",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/OrganizationPostRequest")
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
            $patch = new OrganizationPostRequest($request->all());
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
}
