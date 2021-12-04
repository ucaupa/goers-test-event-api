<?php

namespace App\Models;

class OrderHistory extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method_id',
        'status',
        'data',
    ];
}
