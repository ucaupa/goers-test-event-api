<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\Contracts\IOrganizationRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrganizationRepository extends GenericRepository implements IOrganizationRepository
{
    /**
     * @var  BaseModel
     */
    protected $modelUser;

    public function __construct(User $modelUser)
    {
        parent::__construct(app(Organization::class));
        $this->modelUser = $modelUser;
    }

    public function create($model)
    {
        $organization = Auth::user()->organization;

        if (!empty($organization))
            throw new HttpException(Response::HTTP_OK, 'You have connected to ' . $organization->name . ' organization.');

        $data = $this->model->query()->create($model);

        $this->modelUser->query()
            ->where('id', Auth::user()->id)
            ->update(['organization_id' => $data->id, 'role_id' => 'admin-organization']);

        return $data->onCreated();
    }
}
