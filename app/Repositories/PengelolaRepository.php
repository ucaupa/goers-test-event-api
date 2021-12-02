<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\IPengelolaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PengelolaRepository extends GenericRepository implements IPengelolaRepository
{
    public function __construct()
    {
        parent::__construct(app(User::class));
    }
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user === null) {
            throw new ModelNotFoundException('User not found');
        }

        $user->delete();

        return $user;
    }
    public function updatePengelola($id, $data)
    {
        $user = User::find($id);

        if ($user === null) {
            throw new ModelNotFoundException('User not found');
        }

        if ($user->foto !== null) {
            unlink(base_path('public/pengelola/' . $user->foto));
        }

        return tap($user)->update($data);
    }
}
