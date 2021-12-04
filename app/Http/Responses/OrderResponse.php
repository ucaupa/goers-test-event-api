<?php

namespace App\Http\Responses;

class OrderResponse
{
    /**
     * @var string
     * */
    public $orderId;

    /**
     * @var string
     * */
    public $transactionId;

    /**
     * @var string
     * */
    public $paymentMethodId;

    /**
     * @var string
     * */
    public $status;

    /**
     * @var string
     * */
    public $firstName;

    /**
     * @var string
     * */
    public $lastName;

    /**
     * @var string
     * */
    public $email;

    /**
     * @var string
     * */
    public $phone;

    /**
     * @var string
     * */
    public $gender;

    /**
     * @var string
     * */
    public $paymentInfo;

    public function __construct($model)
    {
        $this->orderId = $model->order_id;
        $this->transactionId = $model->transaction_id;
        $this->paymentMethodId = $model->payment_method_id;
        $this->status = $model->status;
        $this->firstName = $model->first_name;
        $this->lastName = $model->last_name;
        $this->email = $model->email;
        $this->phone = $model->phone_number;
        $this->gender = $model->gender;
        $this->paymentInfo = json_decode($model->data);
    }

    public function serialize()
    {
        return get_object_vars($this);
    }
}
