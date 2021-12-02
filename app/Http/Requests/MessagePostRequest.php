<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *     schema="MessagePostRequest",
 * )
 */
class MessagePostRequest
{
    /**
     * @OA\Property()
     * @var string
     * */
    public $to;

    /**
     * @OA\Property()
     * @var string
     * */
    public $message;

    /**
     * @OA\Property()
     * @var string
     * */
    public $messageKey;

    /**
     * @OA\Property()
     * @var string
     * */
    public $description;

    /**
     * @OA\Property()
     * @var string
     * */
    public $link;

    public function __construct($payload)
    {
        if (empty($payload)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Request payload was empty');
        }

        $validator = Validator::make($payload, [
            'to' => 'required|string|exists:users,username',
            'message' => 'required|string',
            'messageKey' => 'required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator, $validator->errors());

        $this->map((object)$payload);
    }

    private function map($object)
    {
        $this->to = property_exists($object, 'to') ? $object->to : null;
        $this->message = property_exists($object, 'message') ? $object->message : null;
        $this->messageKey = property_exists($object, 'messageKey') ? $object->messageKey : null;
        $this->description = property_exists($object, 'description') ? $object->description : null;
        $this->link = property_exists($object, 'link') ? $object->link : null;
    }

    public function parse()
    {
        $result = array(
            'username_tujuan' => $this->to,
            'username_asal' => Auth::user()->username,
            'kode_organisasi' => Auth::user()->kode_organisasi,
            'dibaca' => 'N',
            'tanggal_kirim' => Carbon::now(),
            'pesan' => $this->message,
            'tag_pesan' => $this->messageKey,
            'description' => $this->description,
            'link' => $this->link,
        );

        return array_filter($result, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
