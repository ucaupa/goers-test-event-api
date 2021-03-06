<?php

namespace App\Models;

class Order extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice',
        'order_id',
        'transaction_id',
        'payment_method_id',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'data',
    ];

    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
}
