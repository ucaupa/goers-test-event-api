<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthChangePasswordPatchRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegistrationPostRequest;
use App\Models\User;
use App\Repositories\Contracts\IUserAuthRepository;
use App\Transformers\UserAuthTransformer;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /*
     * @return IUserAuthRepository
     * */
    protected $repo;

    /**
     * @var JWTAuth
     */
    protected $jwt;

    /**
     * Create a new AuthController instance.
     *
     * @param IUserAuthRepository $repo
     * @param JWTAuth $jwt
     */
    public function __construct(IUserAuthRepository $repo, JWTAuth $jwt)
    {
        $this->middleware("auth:api", ["except" => ["login", "register"]]);
        $this->repo = $repo;
        $this->jwt = $jwt;
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     operationId="authLogin",
     *     tags={"Authentication"},
     *     summary="Get token login",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Login",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LoginRequest")
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
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $patch = new LoginRequest($request->all());
            $credentials = $patch->parse();

            $user = $this->repo->authentication($credentials);

            if (!empty($user))
                if ($jwt = $this->guard()->login($user)) {
                    $token = $this->repo->buildToken($user, $jwt);

                    return $this->buildJsonResponse($this->respondWithToken($token, $user));
                }

            return $this->buildErrorResponse("Unauthorized", Response::HTTP_UNAUTHORIZED);
        } catch (ValidationException $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        } catch (ModelNotFoundException $exception) {
            return $this->buildErrorResponse("User tidak ditemukan", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/register",
     *     operationId="storeUser",
     *     tags={"Authentication"},
     *     summary="Registration new user",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="User",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/UserRegistrationPostRequest")
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
    public function register(Request $request)
    {
        try {
            $patch = new UserRegistrationPostRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->create($model);

            $user = $this->repo->authentication($request->only(['username', 'password']));

            if ($jwt = $this->guard()->login($user)) {
                $token = $this->repo->buildToken($user, $jwt);
                $query['token'] = $token;

                return $this->buildJsonResponse($query);
            }

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
     * @OA\Post(
     *     path="/v1/auth/me",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="authMe",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * */
    public function me()
    {
        $user = $this->guard()->user();

        return $this->buildItemResponse($user, new UserAuthTransformer());
    }

    /**
     * @OA\Patch(
     *     path="/v1/auth/change-password",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="authChangePassword",
     *     tags={"Authentication"},
     *     summary="Change password user",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\RequestBody(
     *         description="Login",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/AuthChangePasswordPatchRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * */
    public function changePassword(Request $request)
    {
        try {
            $patch = new AuthChangePasswordPatchRequest($request->all());
            $model = $patch->parse();

            $query = $this->repo->changePassword($model);

            return response($query, Response::HTTP_OK);
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
     *     path="/v1/auth/logout",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="authLogout",
     *     tags={"Authentication"},
     *     summary="User logout",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * */
    public function logout()
    {
        $this->guard()->logout();

        return $this->buildJsonResponse([
            "message" => "Successfully logged out"
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/refresh",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     operationId="authRefreshToken",
     *     tags={"Authentication"},
     *     summary="User token refresh",
     *     description="",
     *     @OA\Parameter(ref="#/components/parameters/RequestedWith"),
     *     @OA\Response(response=200, ref="#/components/responses/Successful"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/GeneralError")
     * )
     *
     * */
    public function refresh()
    {
        return $this->buildJsonResponse($this->respondWithToken($this->guard()->refresh()));
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @param User|null $user
     * @return array
     */
    protected function respondWithToken($token, $user = null)
    {
        return [
            "access_token" => $token,
            "token_type" => "bearer",
            "expires_in" => $this->guard()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard()
    {
        return Auth::guard("api");
    }
}
