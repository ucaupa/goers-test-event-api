<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EventImageResponse
{
    /**
     * @var string
     * */
    public $id;

    /**
     * @var int
     * */
    public $eventId;

    /**
     * @var string
     * */
    public $fileName;

    /**
     * @var string
     * */
    public $fileUrl;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->eventId = $model->event_id;
        $this->fileName = $model->file_name_original;
        $this->fileUrl = URL::to('/v1/assets/image/event/' . Str::replace('.', '_', $model->file_name));
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
