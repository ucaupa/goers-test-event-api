<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="EventImagePostRequest"
 * )
 */
class EventImagePostRequest
{
    /**
     * @OA\Property(
     *     type="string",
     *     format="binary",
     *     description="{.image format} max: 2Mb",
     * )
     * */
    public $file;
}
