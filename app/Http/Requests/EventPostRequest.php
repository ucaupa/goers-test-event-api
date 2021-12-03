<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="EventPostRequest",
 *     required={"name", "description", "categoryId", "location", "schedule", "image"}
 * )
 */
class EventPostRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $name;

    /**
     * @OA\Property()
     * @var string
     * */
    public $description;

    /**
     * @OA\Property()
     * @var int
     * */
    public $categoryId;

    /**
     * @OA\Property()
     * @var string
     * */
    public $location;

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/EventSchedulePostRequest"
     *     ),
     * )
     * */
    public $schedule;

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *          type="string",
     *          format="binary",
     *     ),
     *     description="{.image format} max: 1Mb",
     * )
     * */
    public $image;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            "name" => "required",
            "description" => "required",
            "categoryId" => "required|exists:event_categories,id",
            "location" => "required",
            "schedule" => "required",
            "schedule.*.startDate" => "required|date",
            "schedule.*.endDate" => "required|date",
            "image" => "required",
            "image.*" => "required|image|max:1024",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->name = property_exists($object, 'name') ? $object->name : null;
        $this->description = property_exists($object, 'description') ? $object->description : null;
        $this->categoryId = property_exists($object, 'categoryId') ? $object->categoryId : null;
        $this->location = property_exists($object, 'location') ? $object->location : null;
        $this->schedule = property_exists($object, 'schedule') ? $object->schedule : null;
        $this->image = property_exists($object, 'image') ? $object->image : null;
    }

    public function parse()
    {
        $result = array(
            'organization_id' => Auth::user()->organization_id,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'location' => $this->location,
            'schedule' => $this->schedule,
            'image' => $this->image,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
