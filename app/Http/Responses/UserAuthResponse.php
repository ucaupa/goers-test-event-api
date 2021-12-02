<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use DateTime;

class UserAuthResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $username;

    /**
     * @var string
     * */
    public $email;

    /**
     * @var string
     * */
    public $name;

    /**
     * @var string
     * */
    public $phoneNumber;

    /**
     * @var integer
     * */
    public $organizationId;

    /**
     * @var OrganizationResponse
     * */
    public $organization;

    /**
     * @var string
     * */
    public $roleId;

    /**
     * @var RoleResponse
     * */
    public $role;

    /**
     * @var DateTime
     */
    public $createdAt;

    /**
     * @var DateTime
     */
    public $updatedAt;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->name = $model->name;
        $this->email = $model->email;
        $this->phoneNumber = $model->phone_number;
        $this->organizationId = $model->organization_id;
        $this->roleId = $model->role_id;
        $this->organization = isset($model->organization) ? new OrganizationResponse($model->organization) : null;
        $this->role = isset($model->role) ? new RoleResponse($model->role) : null;
        $this->createdAt = Carbon::parse($model->created_at);
        $this->updatedAt = Carbon::parse($model->updated_at);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
