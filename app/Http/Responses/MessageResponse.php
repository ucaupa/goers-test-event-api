<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class MessageResponse
{
    /**
     * @var int
     * */
    public $id;

    /**
     * @var string
     * */
    public $to;

    /**
     * @var bool
     * */
    public $isRead;

    /**
     * @var string
     * */
    public $link;

    /**
     * @var string
     * */
    public $message;

    /**
     * @var string
     * */
    public $from;

    /**
     * @var string
     * */
    public $description;

    /**
     * @var string
     * */
    public $messageKey;

    /**
     * @var int
     * */
    public $instansiId;

    /**
     * @var \DateTime
     * */
    public $dateRead;

    /**
     * @var \DateTime
     * */
    public $sentDate;

    public function __construct($model)
    {
        $this->id = $model->id;
        $this->to = $model->username_tujuan;
        $this->isRead = $model->dibaca == 'Y';
        $this->link = $model->link;
        $this->message = $model->pesan;
        $this->from = $model->username_asal;
        $this->description = $model->description;
        $this->messageKey = $model->tag_pesan;
        $this->instansiId = $model->kode_organisasi;
        $this->dateRead = $model->tanggal_baca ? Carbon::parse($model->tanggal_baca) : null;
        $this->sentDate = Carbon::parse($model->tanggal_kirim);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
