<?php

namespace App\Http\Responses;

use Illuminate\Support\Str;

class EventResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var string
     * */
    public $slugId;

    /**
     * @var string
     * */
    public $name;

    /**
     * @var string
     * */
    public $description;

    /**
     * @var int
     * */
    public $organizationId;

    /**
     * @var int
     * */
    public $categoryId;

    /**
     * @var string
     * */
    public $location;

    /**
     * @var bool
     * */
    public $isDraft;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->slugId = Str::slug($model->name) . '--' . $model->id;
        $this->name = $model->name;
        $this->description = $model->description;
        $this->organizationId = (int)$model->organization_id;
        $this->categoryId = (int)$model->category_id;
        $this->location = $model->location;
        $this->isDraft = (bool)$model->is_draft;
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
