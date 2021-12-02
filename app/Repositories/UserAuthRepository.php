<?php

namespace App\Repositories;

use App\Http\Responses\UserAuthResponse;
use App\Models\User;
use App\Repositories\Contracts\IUserAuthRepository;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAuthRepository extends GenericRepository implements IUserAuthRepository
{
    public function __construct()
    {
        parent::__construct(app(User::class));
    }

    public function find($username)
    {
        return $this->model->whereUsername($username)->firstOrFail();
    }

    public function authentication($model)
    {
        $user = $this->model
            ->query()
            ->with(['organization', 'role'])
            ->whereUsername($model['username'])
            ->firstOrFail();

        if (Hash::check($model['password'], $user->password))
            return $user;

        return null;
    }

    public function changePassword($model)
    {
        $data = $this->model->whereUsername(Auth::user()->username)
            ->first();

        if (!Hash::check($model['old_password'], $data->password))
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Old password is wrong');

        if ($model['old_password'] == $model['new_password'])
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Password lama tidak boleh sama dengan yang baru');

        $data->update([
            'password' => Hash::make($model['new_password'])
        ]);

        return null;
    }

    public function buildToken($user, $jwt)
    {
        $key = config('jwt.secret');
        $algo = config('jwt.algo');

        $user = new UserAuthResponse($user);

        $claim = JWT::decode($jwt, $key, [$algo]);
        $claim->iss = config('app.url');
        $claim->xxx = base64_encode(json_encode($user));

        return JWT::encode($claim, $key, $algo);
    }
}
